<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use App\Models\Student;
use App\Models\Book;
use Carbon\Carbon;

class CirculationController extends Controller
{
    public function index()
    {
        // Riwayat terbaru (JOIN + prefix kolom untuk menghindari ambigu)
        $recent = DB::table('loans')
            ->leftJoin('students','students.student_id','=','loans.student_id')
            ->leftJoin('books','books.book_id','=','loans.book_id')
            ->select(
                'loans.*',
                'students.name as student_name',
                'books.title as book_title'
            )
            // pakai loaned_at bila tersedia, fallback ke created_at
            ->orderByDesc(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"))
            ->limit(12)
            ->get();

        // KPI kecil (hari ini)
        $start = now()->startOfDay();
        $end   = now()->endOfDay();

        $loansToday   = Loan::whereBetween(DB::raw("COALESCE(loaned_at, created_at)"), [$start, $end])->count();
        $returnsToday = Loan::whereNotNull('returned_at')
                            ->whereBetween('returned_at', [$start, $end])->count();
        $overdue      = Loan::whereNull('returned_at')->where('due_at','<', now())->count();

        return view('circulation.index', compact('recent','loansToday','returnsToday','overdue'));
    }

    /**
     * Riwayat sirkulasi + filter tanggal & tipe.
     * Route: GET /circulation/history
     */
    public function history(Request $r)
    {
        $type = $r->query('type', 'loans'); // loans|returns|overdue|all
        $from = Carbon::parse($r->query('from', now()->toDateString()))->startOfDay();
        $to   = Carbon::parse($r->query('to',   now()->toDateString()))->endOfDay();

        // base query (JOIN + prefix)
        $q = DB::table('loans')
            ->leftJoin('students','students.student_id','=','loans.student_id')
            ->leftJoin('books','books.book_id','=','loans.book_id')
            ->select(
                'loans.*',
                'students.name as student_name',
                'books.title as book_title'
            );

        if ($type === 'returns') {
            $q->whereNotNull('loans.returned_at')
              ->whereBetween('loans.returned_at', [$from, $to])
              ->orderByDesc('loans.returned_at');
        } elseif ($type === 'overdue') {
            $q->whereNull('loans.returned_at')
              ->where('loans.due_at','<', $to)
              ->orderByDesc('loans.due_at');
        } else { // loans atau all
            $q->whereBetween(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"), [$from, $to])
              ->orderByDesc(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"));
        }

        $rows = $q->paginate(20)->appends($r->query());

        $stats = [
            'loans'   => Loan::whereBetween(DB::raw("COALESCE(loaned_at, created_at)"), [$from,$to])->count(),
            'returns' => Loan::whereNotNull('returned_at')->whereBetween('returned_at', [$from,$to])->count(),
            'overdue' => Loan::whereNull('returned_at')->where('due_at','<',$to)->count(),
        ];

        return view('circulation.history', compact('rows','type','from','to','stats'));
    }

    /**
     * PINJAM BUKU
     * SEKARANG INPUT PAKAI:
     *  - student_barcode
     *  - book_barcode
     */
    public function borrow(Request $r)
    {
        $data = $r->validate([
            'student_barcode' => 'required|string',
            'book_barcode'    => 'required|string',
            'days'            => 'nullable|integer|min:1|max:30',
        ]);

        $days = (int)($data['days'] ?? 7);

        // 1. CARI MASTER SISWA BERDASARKAN BARCODE
        $student = Student::where('barcode', $data['student_barcode'])->first();

        if (! $student) {
            return back()
                ->withErrors([
                    'student_barcode' => 'Siswa dengan barcode ini belum terdaftar di master. Silakan minta admin untuk input siswa dulu.',
                ])
                ->withInput();
        }

        // 2. CARI MASTER BUKU BERDASARKAN BARCODE
        $book = Book::where('barcode', $data['book_barcode'])->first();

        if (! $book) {
            return back()
                ->withErrors([
                    'book_barcode' => 'Buku dengan barcode ini belum terdaftar di master. Silakan minta admin untuk input buku dulu.',
                ])
                ->withInput();
        }

        // 3. CEK STOK BUKU
        if ((int)($book->available_copies ?? 0) < 1) {
            return back()
                ->withErrors([
                    'book_barcode' => 'Stok buku habis / tidak tersedia.',
                ])
                ->withInput();
        }

        // 4. BUAT PINJAMAN
        //    PERHATIKAN: loans.student_id & loans.book_id pakai KODE (student_id, book_id) bukan ID numeric
        $loan = Loan::create([
            'student_id' => $student->student_id,   // dari master
            'book_id'    => $book->book_id,        // dari master
            'days'       => $days,
            'loaned_at'  => now(),
            'due_at'     => now()->addDays($days),
        ]);

        // 5. KURANGI STOK
        $book->update([
            'available_copies' => max(0, (int)$book->available_copies - 1),
        ]);

        return back()->with('ok','Peminjaman dicatat. Jatuh tempo: '.$loan->due_at->format('d M Y'));
    }

    /**
     * PENGEMBALIAN
     * JUGA PAKAI BARCODE, BUKAN KODE MANUAL
     */
    public function return(Request $r)
    {
        $data = $r->validate([
            'student_barcode' => 'required|string',
            'book_barcode'    => 'required|string',
        ]);

        // 1. CARI SISWA & BUKU DI MASTER
        $student = Student::where('barcode', $data['student_barcode'])->first();
        $book    = Book::where('barcode', $data['book_barcode'])->first();

        if (! $student) {
            return back()
                ->withErrors([
                    'student_barcode' => 'Siswa dengan barcode ini tidak ditemukan di master.',
                ])
                ->withInput();
        }

        if (! $book) {
            return back()
                ->withErrors([
                    'book_barcode' => 'Buku dengan barcode ini tidak ditemukan di master.',
                ])
                ->withInput();
        }

        // 2. CARI TRANSAKSI PINJAM YANG MASIH AKTIF
        $loan = Loan::whereNull('returned_at')
            ->where('student_id', $student->student_id) // pakai kode
            ->where('book_id', $book->book_id)
            ->orderByDesc(DB::raw("COALESCE(loaned_at, created_at)"))
            ->first();

        if (! $loan) {
            return back()
                ->withErrors([
                    'book_barcode' => 'Transaksi peminjaman aktif untuk buku ini oleh siswa tersebut tidak ditemukan.',
                ])
                ->withInput();
        }

        // 3. UPDATE PENGEMBALIAN
        $loan->update([
            'returned_at' => now(),
        ]);

        // 4. TAMBAH STOK
        $book->increment('available_copies');

        return back()->with('ok','Pengembalian dicatat.');
    }
}