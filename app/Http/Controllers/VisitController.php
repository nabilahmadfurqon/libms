<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Student; // ⬅️ TAMBAHAN: pakai model Student
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitController extends Controller
{
    /**
     * Halaman scan + daftar kunjungan hari ini
     */
    public function index(Request $request)
    {
        // Daftar kunjungan hari ini (pakai visited_at, bukan created_at)
        $today = Visit::query()
            ->leftJoin('students', 'students.student_id', '=', 'visits.student_id')
            ->select(
                'visits.*',
                'students.name as student_name',
                'students.kelas as student_class',
            )
            ->today()
            ->orderByDesc('visited_at')
            ->paginate(15);

        // Total kunjungan hari ini untuk badge di header
        $visitsToday = Visit::today()->count();

        return view('visits.index', compact('today', 'visitsToday'));
    }

    /**
     * Riwayat kunjungan dengan filter tanggal
     */
    public function history(Request $request)
    {
        $from = $request->query('from', today()->toDateString());
        $to   = $request->query('to',   today()->toDateString());

        // Query utama untuk tabel (dengan join student)
        $rows = Visit::query()
            ->leftJoin('students', 'students.student_id', '=', 'visits.student_id')
            ->select(
                'visits.*',
                'students.name as student_name',
                'students.kelas as student_class',
            )
            ->whereDate('visited_at', '>=', $from)
            ->whereDate('visited_at', '<=', $to)
            ->orderByDesc('visited_at')
            ->paginate(20)
            ->withQueryString();

        // Base query untuk hitung ringkasan
        $base = Visit::whereDate('visited_at', '>=', $from)
            ->whereDate('visited_at', '<=', $to);

        $count                = (clone $base)->count();                                // semua kunjungan
        $classVisitCount      = (clone $base)->whereNotNull('class_name')->count();    // berapa kali "kunjungan kelas"
        $classStudentSum      = (clone $base)->whereNotNull('class_name')->sum('student_count'); // total siswa kunj. kelas
        $individualVisitCount = $count - $classVisitCount;                             // kunjungan perorangan

        return view('visits.history', compact(
            'rows',
            'from',
            'to',
            'count',
            'classVisitCount',
            'classStudentSum',
            'individualVisitCount'
        ));
    }

    /**
     * Simpan kunjungan baru (hasil scan barcode atau input manual per siswa)
     * Aturan:
     *  - Barcode HARUS sudah ada di master students.barcode
     *  - 1 siswa hanya boleh 1x kunjungan per hari.
     */
    public function store(Request $r)
    {
        // ⬅️ DI SINI BERUBAH: kita terima student_barcode, bukan langsung student_id
        $data = $r->validate([
            'student_barcode' => 'required|string',
            'purpose'         => 'nullable|string|max:50',
        ]);

        // 1. CARI SISWA DI MASTER BERDASARKAN BARCODE
        $student = Student::where('barcode', $data['student_barcode'])->first();

        if (! $student) {
            // ❌ BARCODE TIDAK ADA DI MASTER → STOP, JANGAN BUAT VISIT
            return back()
                ->withErrors([
                    'student_barcode' => 'Siswa dengan barcode ini belum terdaftar di master. Silakan minta admin menambahkan data siswa terlebih dahulu.',
                ])
                ->withInput();
        }

        $today = Carbon::today()->toDateString();

        // 2. CEK: apakah siswa ini sudah melakukan visit hari ini?
        $alreadyVisited = Visit::where('student_id', $student->student_id)
            ->whereDate('visited_at', $today)
            ->exists();

        if ($alreadyVisited) {
            return back()
                ->withErrors([
                    'student_barcode' => 'Siswa ini sudah tercatat kunjungan hari ini.',
                ])
                ->withInput();
        }

        // 3. SIMPAN KUNJUNGAN (PAKAI student_id DARI MASTER)
        Visit::create([
            'student_id' => $student->student_id,     // tetap pakai kode siswa (NIS)
            'purpose'    => $data['purpose'] ?? null,
            'visited_at' => now(),
        ]);

        return back()->with('ok', 'Kunjungan untuk ' . $student->name . ' berhasil dicatat.');
    }

    /**
     * Simpan kunjungan kelas (rombongan)
     *
     * Aturan: 1 kelas hanya boleh 1x kunjungan kelas per hari.
     *
     * Field:
     * - grade         (mis. VII / VIII / X)
     * - class         (mis. A / IPA 1)
     * - student_count (jumlah siswa)
     * - purpose       (tujuan kunjungan kelas)
     */
    public function storeClass(Request $request)
    {
        $data = $request->validate([
            'grade'         => 'required|string|max:20',
            'class'         => 'required|string|max:50',
            'student_count' => 'required|integer|min:1|max:100',
            'purpose'       => 'nullable|string|max:50',
        ]);

        $className = trim($data['grade'] . ' ' . $data['class']);
        $today     = Carbon::today()->toDateString();

        // CEK: apakah kelas ini sudah tercatat kunjungan kelas hari ini?
        $alreadyClassVisit = Visit::whereDate('visited_at', $today)
            ->where('class_name', $className)
            ->exists();

        if ($alreadyClassVisit) {
            return back()
                ->withErrors(['class' => 'Kunjungan kelas ' . $className . ' sudah tercatat hari ini.'])
                ->withInput();
        }

        Visit::create([
            // supaya tidak null, kita isi pattern khusus
            'student_id'    => 'KELAS ' . $className,
            'class_name'    => $className,
            'student_count' => $data['student_count'],
            'purpose'       => $data['purpose'] ?: 'kunjungan_kelas',
            'visited_at'    => now(),
        ]);

        return back()->with('ok', 'Kunjungan kelas berhasil dicatat.');
    }
}