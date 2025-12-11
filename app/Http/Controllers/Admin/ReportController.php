<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Halaman index laporan.
     */
    public function index(Request $request)
    {
        // ===== RANGE WAKTU (dibatasi maksimal 6 bulan) =====
        $today       = Carbon::today();
        $defaultFrom = $today->copy()->subMonth()->toDateString();  // default: 1 bulan ke belakang
        $defaultTo   = $today->toDateString();

        $from = $request->query('from', $defaultFrom);
        $to   = $request->query('to',   $defaultTo);

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate   = Carbon::parse($to)->endOfDay();

        // Kalau from > to, tukar
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // Batasi maksimal 6 bulan
        if ($fromDate->diffInMonths($toDate) > 6) {
            $toDate = $fromDate->copy()->addMonths(6)->endOfDay();
        }

        // Normalisasi lagi ke string untuk dikirim ke view
        $from = $fromDate->toDateString();
        $to   = $toDate->toDateString();

        // ========= TOTAL DALAM RANGE (UNTUK CARD) =========
        $totalLoansRange   = Loan::whereBetween('created_at', [$fromDate, $toDate])->count();
        $totalReturnsRange = Loan::whereNotNull('returned_at')
                                  ->whereBetween('returned_at', [$fromDate, $toDate])
                                  ->count();
        $totalVisitsRange  = Visit::whereBetween('visited_at', [$fromDate, $toDate])->count();

        // Semua transaksi yang terlambat (untuk total & top 3)
        $lateLoansAll = $this->baseLateLoansQuery($fromDate, $toDate)
            ->get()
            ->map(function ($row) {
                $due = Carbon::parse($row->due_at);
                $end = $row->returned_at ? Carbon::parse($row->returned_at) : now();

                $daysLate = $end->greaterThan($due)
                    ? $due->diffInDays($end)
                    : 0;

                $row->days_late = $daysLate;
                return $row;
            })
            ->filter(fn ($row) => $row->days_late > 0);

        $totalLateLoansRange = $lateLoansAll->count();

        // ===== TOP 3: BUKU FAVORIT =====
        $topBooks = DB::table('loans')
            ->join('books', 'books.id', '=', 'loans.book_id')
            ->select('books.id', 'books.title', 'books.author', DB::raw('COUNT(loans.id) as total'))
            ->whereBetween('loans.created_at', [$fromDate, $toDate])
            ->groupBy('books.id', 'books.title', 'books.author')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $topBooksTotal = $topBooks->sum('total');
        $topBooksChart = [
            'labels' => $topBooks->pluck('title'),
            'data'   => $topBooks->pluck('total'),
        ];

        // ===== TOP 3: PEMINJAM =====
        $topBorrowers = DB::table('loans')
            ->join('students', 'students.student_id', '=', 'loans.student_id')
            ->select('students.student_id', 'students.name', DB::raw('COUNT(loans.id) as total'))
            ->whereBetween('loans.created_at', [$fromDate, $toDate])
            ->groupBy('students.student_id', 'students.name')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $topBorrowersTotal = $topBorrowers->sum('total');
        $topBorrowersChart = [
            'labels' => $topBorrowers->pluck('name'),
            'data'   => $topBorrowers->pluck('total'),
        ];

        // ===== TOP 3: PENGUNJUNG (INDIVIDU) =====
        $topVisitors = DB::table('visits')
            ->join('students', 'students.student_id', '=', 'visits.student_id')
            ->select('students.student_id', 'students.name', DB::raw('COUNT(visits.id) as total'))
            ->whereBetween('visits.visited_at', [$fromDate, $toDate])
            ->groupBy('students.student_id', 'students.name')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $topVisitorsTotal = $topVisitors->sum('total');
        $topVisitorsChart = [
            'labels' => $topVisitors->pluck('name'),
            'data'   => $topVisitors->pluck('total'),
        ];

        // ===== TOP 3: KETERLAMBATAN =====
        $lateLoansTop = $lateLoansAll
            ->sortByDesc('days_late')
            ->take(3)
            ->values();

        $topLateChart = [
            'labels' => $lateLoansTop->map(fn ($r) => $r->student_name),
            'data'   => $lateLoansTop->pluck('days_late'),
        ];

        // ===== RINGKASAN HARIAN / MINGGUAN / BULANAN =====
        $summaries   = $this->buildSummaries($fromDate, $toDate, $lateLoansAll);
        $chartDaily  = $summaries['daily'];
        $chartWeekly = $summaries['weekly'];
        $chartMonthly= $summaries['monthly'];

        return view('admin.reports', compact(
            'from', 'to',
            'totalLoansRange', 'totalReturnsRange', 'totalVisitsRange', 'totalLateLoansRange',
            'topBooks', 'topBorrowers', 'topVisitors', 'lateLoansTop',
            'topBooksTotal', 'topBorrowersTotal', 'topVisitorsTotal',
            'topBooksChart', 'topBorrowersChart', 'topVisitorsChart', 'topLateChart',
            'chartDaily', 'chartWeekly', 'chartMonthly'
        ));
    }

    /**
     * Export HARIAN + MINGGUAN + BULANAN + DETAIL (kunjungan, kelas, loan & keterlambatan) ke CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $today       = Carbon::today();
        $defaultFrom = $today->copy()->subMonth()->toDateString();
        $defaultTo   = $today->toDateString();

        $from = $request->query('from', $defaultFrom);
        $to   = $request->query('to',   $defaultTo);

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate   = Carbon::parse($to)->endOfDay();

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        if ($fromDate->diffInMonths($toDate) > 6) {
            $toDate = $fromDate->copy()->addMonths(6)->endOfDay();
        }

        // Ambil semua keterlambatan untuk perhitungan ringkasan & detail keterlambatan
        $lateLoansAll = $this->baseLateLoansQuery($fromDate, $toDate)
            ->get()
            ->map(function ($row) {
                $due = Carbon::parse($row->due_at);
                $end = $row->returned_at ? Carbon::parse($row->returned_at) : now();

                $daysLate = $end->greaterThan($due)
                    ? $due->diffInDays($end)
                    : 0;

                $row->days_late = $daysLate;
                return $row;
            })
            ->filter(fn ($row) => $row->days_late > 0);

        $summaries = $this->buildSummaries($fromDate, $toDate, $lateLoansAll);

        // ===== DETAIL KUNJUNGAN INDIVIDU =====
        $visitsDetail = Visit::query()
            ->join('students', 'students.student_id', '=', 'visits.student_id') // hanya yang punya data siswa
            ->whereBetween('visits.visited_at', [$fromDate, $toDate])
            ->orderBy('visits.visited_at')
            ->select(
                'visits.visited_at',
                'students.student_id',
                'students.name'
            )
            ->get();

        // ===== DETAIL KUNJUNGAN KELAS =====
        $classVisitsDetail = Visit::query()
            ->whereBetween('visited_at', [$fromDate, $toDate])
            ->whereNotNull('class_name')
            ->orderBy('visited_at')
            ->get([
                'visited_at',
                'class_name',
                'student_count',
                'purpose',
            ]);

        // ===== DETAIL PEMINJAMAN (loan + siswa + buku, termasuk keterlambatan per baris) =====
        $loansDetail = Loan::query()
            ->leftJoin('students', 'students.student_id', '=', 'loans.student_id')
            ->leftJoin('books', 'books.id', '=', 'loans.book_id')
            ->whereBetween('loans.created_at', [$fromDate, $toDate])
            ->orderBy('loans.created_at')
            ->select(
                'loans.created_at',
                'students.student_id',
                'students.name as student_name',
                'books.title as book_title',
                'loans.due_at',
                'loans.returned_at'
            )
            ->get()
            ->map(function ($row) {
                $due = $row->due_at ? Carbon::parse($row->due_at) : null;
                $ret = $row->returned_at ? Carbon::parse($row->returned_at) : null;

                $daysLate = 0;
                if ($due) {
                    $end = $ret ?: now();
                    if ($end->greaterThan($due)) {
                        $daysLate = $due->diffInDays($end);
                    }
                }

                $row->days_late = $daysLate;
                return $row;
            });

        $filename = 'laporan_ringkasan_' . $fromDate->format('Ymd') . '_' . $toDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use (
            $summaries,
            $fromDate,
            $toDate,
            $visitsDetail,
            $classVisitsDetail,
            $loansDetail,
            $lateLoansAll
        ) {
            $handle = fopen('php://output', 'w');

            // ===== RINGKASAN HARIAN =====
            fputcsv($handle, ['RINGKASAN HARIAN', $fromDate->toDateString(), $toDate->toDateString()]);
            fputcsv($handle, [
                'Tanggal',
                'Kunjungan (total)',
                'Kunjungan Kelas (rombongan)',
                'Siswa Kunjungan Kelas',
                'Peminjaman',
                'Pengembalian',
                'Keterlambatan'
            ]);

            foreach ($summaries['daily']['labels'] as $i => $label) {
                fputcsv($handle, [
                    $label,
                    $summaries['daily']['visits'][$i] ?? 0,
                    $summaries['daily']['class_visits'][$i] ?? 0,
                    $summaries['daily']['class_students'][$i] ?? 0,
                    $summaries['daily']['loans'][$i] ?? 0,
                    $summaries['daily']['returns'][$i] ?? 0,
                    $summaries['daily']['late'][$i] ?? 0,
                ]);
            }

            fputcsv($handle, []); // baris kosong

            // ===== RINGKASAN MINGGUAN =====
            fputcsv($handle, ['RINGKASAN MINGGUAN']);
            fputcsv($handle, [
                'Minggu',
                'Kunjungan (total)',
                'Kunjungan Kelas (rombongan)',
                'Siswa Kunjungan Kelas',
                'Peminjaman',
                'Pengembalian',
                'Keterlambatan'
            ]);

            foreach ($summaries['weekly']['labels'] as $i => $label) {
                fputcsv($handle, [
                    $label,
                    $summaries['weekly']['visits'][$i] ?? 0,
                    $summaries['weekly']['class_visits'][$i] ?? 0,
                    $summaries['weekly']['class_students'][$i] ?? 0,
                    $summaries['weekly']['loans'][$i] ?? 0,
                    $summaries['weekly']['returns'][$i] ?? 0,
                    $summaries['weekly']['late'][$i] ?? 0,
                ]);
            }

            fputcsv($handle, []);

            // ===== RINGKASAN BULANAN =====
            fputcsv($handle, ['RINGKASAN BULANAN']);
            fputcsv($handle, [
                'Bulan',
                'Kunjungan (total)',
                'Kunjungan Kelas (rombongan)',
                'Siswa Kunjungan Kelas',
                'Peminjaman',
                'Pengembalian',
                'Keterlambatan'
            ]);

            foreach ($summaries['monthly']['labels'] as $i => $label) {
                fputcsv($handle, [
                    $label,
                    $summaries['monthly']['visits'][$i] ?? 0,
                    $summaries['monthly']['class_visits'][$i] ?? 0,
                    $summaries['monthly']['class_students'][$i] ?? 0,
                    $summaries['monthly']['loans'][$i] ?? 0,
                    $summaries['monthly']['returns'][$i] ?? 0,
                    $summaries['monthly']['late'][$i] ?? 0,
                ]);
            }

            // ===== BARIS KOSONG PEMISAH =====
            fputcsv($handle, []);
            fputcsv($handle, []);

            // ===== DETAIL KUNJUNGAN (INDIVIDU) =====
            fputcsv($handle, ['DATA DETAIL KUNJUNGAN (INDIVIDU)']);
            fputcsv($handle, ['Tanggal Kunjungan', 'Student ID', 'Nama Siswa']);

            foreach ($visitsDetail as $v) {
                $dateStr = $v->visited_at
                    ? Carbon::parse($v->visited_at)->format('Y-m-d H:i')
                    : '';
                fputcsv($handle, [
                    $dateStr,
                    $v->student_id,
                    $v->name,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, []);

            // ===== DETAIL KUNJUNGAN KELAS =====
            fputcsv($handle, ['DATA DETAIL KUNJUNGAN KELAS']);
            fputcsv($handle, ['Tanggal Kunjungan', 'Nama Kelas', 'Jumlah Siswa', 'Tujuan']);

            foreach ($classVisitsDetail as $cv) {
                $dateStr = $cv->visited_at
                    ? Carbon::parse($cv->visited_at)->format('Y-m-d H:i')
                    : '';
                fputcsv($handle, [
                    $dateStr,
                    $cv->class_name,
                    $cv->student_count,
                    $cv->purpose,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, []);

            // ===== DETAIL PEMINJAMAN =====
            fputcsv($handle, ['DATA DETAIL PEMINJAMAN']);
            fputcsv($handle, [
                'Tanggal Pinjam',
                'Student ID',
                'Nama Siswa',
                'Judul Buku',
                'Jatuh Tempo',
                'Tanggal Kembali',
                'Hari Terlambat'
            ]);

            foreach ($loansDetail as $loan) {
                $loanDate = $loan->created_at
                    ? Carbon::parse($loan->created_at)->format('Y-m-d H:i')
                    : '';
                $dueDate  = $loan->due_at
                    ? Carbon::parse($loan->due_at)->format('Y-m-d')
                    : '';
                $retDate  = $loan->returned_at
                    ? Carbon::parse($loan->returned_at)->format('Y-m-d H:i')
                    : '';

                fputcsv($handle, [
                    $loanDate,
                    $loan->student_id,
                    $loan->student_name,
                    $loan->book_title,
                    $dueDate,
                    $retDate,
                    $loan->days_late,
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, []);

            // ===== DETAIL KETERLAMBATAN SAJA =====
            fputcsv($handle, ['DATA DETAIL KETERLAMBATAN (HANYA YANG TERLAMBAT)']);
            fputcsv($handle, [
                'Student ID',
                'Nama Siswa',
                'Judul Buku',
                'Jatuh Tempo',
                'Tanggal Kembali / Sekarang',
                'Hari Terlambat'
            ]);

            foreach ($lateLoansAll as $row) {
                $dueDate = $row->due_at
                    ? Carbon::parse($row->due_at)->format('Y-m-d')
                    : '';
                $retDate = $row->returned_at
                    ? Carbon::parse($row->returned_at)->format('Y-m-d H:i')
                    : 'BELUM KEMBALI';

                fputcsv($handle, [
                    $row->student_id,
                    $row->student_name,
                    $row->book_title,
                    $dueDate,
                    $retDate,
                    $row->days_late,
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Query dasar untuk mengambil transaksi yang terlambat.
     * Dipakai untuk summary & detail keterlambatan.
     */
    private function baseLateLoansQuery(Carbon $fromDate, Carbon $toDate)
    {
        return Loan::query()
            ->join('students', 'students.student_id', '=', 'loans.student_id')
            ->join('books', 'books.id', '=', 'loans.book_id')
            ->whereBetween('loans.due_at', [$fromDate, $toDate])
            ->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNotNull('returned_at')
                        ->whereColumn('returned_at', '>', 'due_at');
                })->orWhere(function ($qq) {
                    $qq->whereNull('returned_at')
                        ->where('due_at', '<', now());
                });
            })
            ->select(
                'students.student_id',
                'students.name as student_name',
                'books.title as book_title',
                'loans.due_at',
                'loans.returned_at'
            );
    }

    /**
     * Build summary harian / mingguan / bulanan.
     * Termasuk:
     * - Kunjungan total
     * - Kunjungan kelas (rombongan & jumlah siswa)
     * - Peminjaman
     * - Pengembalian
     * - Keterlambatan
     */
    private function buildSummaries(Carbon $fromDate, Carbon $toDate, $lateLoansAll): array
    {
        $visitsRange  = Visit::whereBetween('visited_at', [$fromDate, $toDate])->get();
        $loansRange   = Loan::whereBetween('created_at', [$fromDate, $toDate])->get();
        $returnsRange = Loan::whereNotNull('returned_at')
                            ->whereBetween('returned_at', [$fromDate, $toDate])
                            ->get();

        $classVisitsRange = $visitsRange->filter(fn ($v) => !is_null($v->class_name));

        /* ---------------- HARIAN ---------------- */
        $vByDay   = $visitsRange->groupBy(fn ($v) => Carbon::parse($v->visited_at)->toDateString());
        $lByDay   = $loansRange->groupBy(fn ($r) => Carbon::parse($r->created_at)->toDateString());
        $rByDay   = $returnsRange->groupBy(fn ($r) => Carbon::parse($r->returned_at)->toDateString());
        $lateByDay= $lateLoansAll->groupBy(fn ($r) => Carbon::parse($r->due_at)->toDateString());

        $classVisitsByDay    = [];
        $classStudentsByDay  = [];

        foreach ($classVisitsRange as $cv) {
            $d = Carbon::parse($cv->visited_at)->toDateString();
            $classVisitsByDay[$d]   = ($classVisitsByDay[$d] ?? 0) + 1;
            $classStudentsByDay[$d] = ($classStudentsByDay[$d] ?? 0) + (int)($cv->student_count ?? 0);
        }

        $dailyLabels        = [];
        $dailyVisits        = [];
        $dailyLoans         = [];
        $dailyReturns       = [];
        $dailyLate          = [];
        $dailyClassVisits   = [];
        $dailyClassStudents = [];

        $period = CarbonPeriod::create($fromDate->copy()->startOfDay(), $toDate->copy()->startOfDay());

        foreach ($period as $day) {
            $dateStr       = $day->toDateString();
            $dailyLabels[] = $day->format('d M');

            $dailyVisits[]        = isset($vByDay[$dateStr])   ? $vByDay[$dateStr]->count()   : 0;
            $dailyLoans[]         = isset($lByDay[$dateStr])   ? $lByDay[$dateStr]->count()   : 0;
            $dailyReturns[]       = isset($rByDay[$dateStr])   ? $rByDay[$dateStr]->count()   : 0;
            $dailyLate[]          = isset($lateByDay[$dateStr])? $lateByDay[$dateStr]->count(): 0;
            $dailyClassVisits[]   = $classVisitsByDay[$dateStr]   ?? 0;
            $dailyClassStudents[] = $classStudentsByDay[$dateStr] ?? 0;
        }

        $daily = [
            'labels'         => $dailyLabels,
            'visits'         => $dailyVisits,
            'loans'          => $dailyLoans,
            'returns'        => $dailyReturns,
            'late'           => $dailyLate,
            'class_visits'   => $dailyClassVisits,
            'class_students' => $dailyClassStudents,
        ];

        /* ---------------- MINGGUAN ---------------- */
        $vByWeek    = $visitsRange->groupBy(fn ($v) => Carbon::parse($v->visited_at)->isoFormat('GGGG-[W]WW'));
        $lByWeek    = $loansRange->groupBy(fn ($r) => Carbon::parse($r->created_at)->isoFormat('GGGG-[W]WW'));
        $rByWeek    = $returnsRange->groupBy(fn ($r) => Carbon::parse($r->returned_at)->isoFormat('GGGG-[W]WW'));
        $lateByWeek = $lateLoansAll->groupBy(fn ($r) => Carbon::parse($r->due_at)->isoFormat('GGGG-[W]WW'));

        $classVisitsByWeek   = [];
        $classStudentsByWeek = [];
        foreach ($classVisitsRange as $cv) {
            $wk = Carbon::parse($cv->visited_at)->isoFormat('GGGG-[W]WW');
            $classVisitsByWeek[$wk]   = ($classVisitsByWeek[$wk] ?? 0) + 1;
            $classStudentsByWeek[$wk] = ($classStudentsByWeek[$wk] ?? 0) + (int)($cv->student_count ?? 0);
        }

        $allWeekKeys = collect(array_merge(
            $vByWeek->keys()->all(),
            $lByWeek->keys()->all(),
            $rByWeek->keys()->all(),
            $lateByWeek->keys()->all(),
            array_keys($classVisitsByWeek),
            array_keys($classStudentsByWeek)
        ))->unique()->sort()->values();

        $weeklyLabels        = [];
        $weeklyVisits        = [];
        $weeklyLoans         = [];
        $weeklyReturns       = [];
        $weeklyLate          = [];
        $weeklyClassVisits   = [];
        $weeklyClassStudents = [];

        foreach ($allWeekKeys as $weekKey) {
            [$year, $weekStr] = explode('-W', $weekKey);
            $weeklyLabels[] = "Minggu {$weekStr} ({$year})";

            $weeklyVisits[]        = isset($vByWeek[$weekKey])   ? $vByWeek[$weekKey]->count()   : 0;
            $weeklyLoans[]         = isset($lByWeek[$weekKey])   ? $lByWeek[$weekKey]->count()   : 0;
            $weeklyReturns[]       = isset($rByWeek[$weekKey])   ? $rByWeek[$weekKey]->count()   : 0;
            $weeklyLate[]          = isset($lateByWeek[$weekKey])? $lateByWeek[$weekKey]->count(): 0;
            $weeklyClassVisits[]   = $classVisitsByWeek[$weekKey]   ?? 0;
            $weeklyClassStudents[] = $classStudentsByWeek[$weekKey] ?? 0;
        }

        $weekly = [
            'labels'         => $weeklyLabels,
            'visits'         => $weeklyVisits,
            'loans'          => $weeklyLoans,
            'returns'        => $weeklyReturns,
            'late'           => $weeklyLate,
            'class_visits'   => $weeklyClassVisits,
            'class_students' => $weeklyClassStudents,
        ];

        /* ---------------- BULANAN ---------------- */
        $vByMonth    = $visitsRange->groupBy(fn ($v) => Carbon::parse($v->visited_at)->format('Y-m'));
        $lByMonth    = $loansRange->groupBy(fn ($r) => Carbon::parse($r->created_at)->format('Y-m'));
        $rByMonth    = $returnsRange->groupBy(fn ($r) => Carbon::parse($r->returned_at)->format('Y-m'));
        $lateByMonth = $lateLoansAll->groupBy(fn ($r) => Carbon::parse($r->due_at)->format('Y-m'));

        $classVisitsByMonth   = [];
        $classStudentsByMonth = [];
        foreach ($classVisitsRange as $cv) {
            $mk = Carbon::parse($cv->visited_at)->format('Y-m');
            $classVisitsByMonth[$mk]   = ($classVisitsByMonth[$mk] ?? 0) + 1;
            $classStudentsByMonth[$mk] = ($classStudentsByMonth[$mk] ?? 0) + (int)($cv->student_count ?? 0);
        }

        $allMonthKeys = collect(array_merge(
            $vByMonth->keys()->all(),
            $lByMonth->keys()->all(),
            $rByMonth->keys()->all(),
            $lateByMonth->keys()->all(),
            array_keys($classVisitsByMonth),
            array_keys($classStudentsByMonth)
        ))->unique()->sort()->values();

        $monthlyLabels        = [];
        $monthlyVisits        = [];
        $monthlyLoans         = [];
        $monthlyReturns       = [];
        $monthlyLate          = [];
        $monthlyClassVisits   = [];
        $monthlyClassStudents = [];

        foreach ($allMonthKeys as $monthKey) {
            $monthlyLabels[] = Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F Y');

            $monthlyVisits[]        = isset($vByMonth[$monthKey])   ? $vByMonth[$monthKey]->count()   : 0;
            $monthlyLoans[]         = isset($lByMonth[$monthKey])   ? $lByMonth[$monthKey]->count()   : 0;
            $monthlyReturns[]       = isset($rByMonth[$monthKey])   ? $rByMonth[$monthKey]->count()   : 0;
            $monthlyLate[]          = isset($lateByMonth[$monthKey])? $lateByMonth[$monthKey]->count(): 0;
            $monthlyClassVisits[]   = $classVisitsByMonth[$monthKey]   ?? 0;
            $monthlyClassStudents[] = $classStudentsByMonth[$monthKey] ?? 0;
        }

        $monthly = [
            'labels'         => $monthlyLabels,
            'visits'         => $monthlyVisits,
            'loans'          => $monthlyLoans,
            'returns'        => $monthlyReturns,
            'late'           => $monthlyLate,
            'class_visits'   => $monthlyClassVisits,
            'class_students' => $monthlyClassStudents,
        ];

        return compact('daily', 'weekly', 'monthly');
    }
}
