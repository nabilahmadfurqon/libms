<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
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

        // KPI kecil (hari ini) – gunakan rentang waktu agar portable
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
     * Route yang dipakai: GET /circulation/history
     * Query: ?type=loans|returns|overdue|all&from=YYYY-MM-DD&to=YYYY-MM-DD
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

        // filter per type
        if ($type === 'returns') {
            $q->whereNotNull('loans.returned_at')
              ->whereBetween('loans.returned_at', [$from, $to])
              ->orderByDesc('loans.returned_at');
        } elseif ($type === 'overdue') {
            $q->whereNull('loans.returned_at')
              ->where('loans.due_at','<', $to)
              ->orderByDesc('loans.due_at');
        } elseif ($type === 'all') {
            $q->whereBetween(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"), [$from, $to])
              ->orderByDesc(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"));
        } else { // loans
            $q->whereBetween(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"), [$from, $to])
              ->orderByDesc(DB::raw("COALESCE(loans.loaned_at, loans.created_at)"));
        }

        $rows = $q->paginate(20)->appends($r->query());

        // Statistik ringkas (tanpa JOIN supaya cepat & bebas ambigu)
        $stats = [
            'loans'   => Loan::whereBetween(DB::raw("COALESCE(loaned_at, created_at)"), [$from,$to])->count(),
            'returns' => Loan::whereNotNull('returned_at')->whereBetween('returned_at', [$from,$to])->count(),
            'overdue' => Loan::whereNull('returned_at')->where('due_at','<',$to)->count(),
        ];

        return view('circulation.history', compact('rows','type','from','to','stats'));
    }

    public function borrow(Request $r)
    {
        $data = $r->validate([
            'student_id' => 'required|string',
            'book_id'    => 'required|string',
            'days'       => 'nullable|integer|min:1|max:30',
        ]);

        $days = (int)($data['days'] ?? 7);

        // cek buku
        $book = DB::table('books')->where('book_id',$data['book_id'])->first();
        if (!$book) {
            return back()->with('err','Buku tidak ditemukan.');
        }
        if ((int)($book->available_copies ?? 0) < 1) {
            return back()->with('err','Stok buku habis.');
        }

        // buat pinjaman
        $loan = Loan::create([
            'student_id' => $data['student_id'],
            'book_id'    => $data['book_id'],
            'days'       => $days,
            'loaned_at'  => now(),
            'due_at'     => now()->addDays($days),
        ]);

        // kurangi stok
        DB::table('books')
            ->where('book_id',$data['book_id'])
            ->update([
                'available_copies' => max(0, (int)$book->available_copies - 1)
            ]);

        return back()->with('ok','Peminjaman dicatat. Jatuh tempo: '.$loan->due_at->format('d M Y'));
    }

    public function return(Request $r)
    {
        $data = $r->validate([
            'student_id' => 'required|string',
            'book_id'    => 'required|string',
        ]);

        $loan = Loan::whereNull('returned_at')
            ->where('student_id',$data['student_id'])
            ->where('book_id',$data['book_id'])
            ->orderByDesc(DB::raw("COALESCE(loaned_at, created_at)"))
            ->first();

        if (!$loan) {
            return back()->with('err','Transaksi aktif tidak ditemukan.');
        }

        $loan->update(['returned_at' => now()]);

        DB::table('books')
            ->where('book_id',$data['book_id'])
            ->increment('available_copies');

        return back()->with('ok','Pengembalian dicatat.');
    }
}
