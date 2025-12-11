<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Student;
use App\Models\Loan;
use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    /**
     * Dashboard Admin (single action).
     */
    public function __invoke(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        // ===== TANGGAL DASAR =====
        // Untuk chart harian & mingguan
        if ($request->filled('start_date')) {
            $currentDate = Carbon::parse($request->start_date, $tz)->startOfDay();
        } else {
            $currentDate = Carbon::now($tz)->startOfDay();
        }

        $dateString = $currentDate->toDateString();

        // ===== KPI UTAMA =====
        $totalBooks     = (int) Book::sum('total_copies');
        $activeStudents = (int) Student::count();
        $onLoan         = (int) Loan::whereNull('returned_at')->count();

        // Total kunjungan hari ini
        $visitsToday = (int) Visit::whereDate('visited_at', $dateString)->count();

        // ===== KUNJUNGAN KELAS vs PERORANGAN =====
        $visitsTodayClass = (int) Visit::whereDate('visited_at', $dateString)
            ->whereNotNull('class_name')   // baris yang punya class_name dianggap kunjungan kelas
            ->count();

        $visitsTodayIndividual = max(0, $visitsToday - $visitsTodayClass);

        // ===== CHART HARIAN (per jam) =====
        $hours   = range(0, 23);
        $hLabels = array_map(fn ($h) => sprintf('%02d:00', $h), $hours);
        $hLoans = $hReturns = $hVisits = array_fill(0, 24, 0);

        // Pinjam per jam
        Loan::whereDate('created_at', $dateString)
            ->get()
            ->each(function (Loan $loan) use (&$hLoans, $tz) {
                $hour = (int) Carbon::parse($loan->created_at)->setTimezone($tz)->format('G');
                if (isset($hLoans[$hour])) $hLoans[$hour]++;
            });

        // Kembali per jam
        Loan::whereNotNull('returned_at')
            ->whereDate('returned_at', $dateString)
            ->get()
            ->each(function (Loan $loan) use (&$hReturns, $tz) {
                $hour = (int) Carbon::parse($loan->returned_at)->setTimezone($tz)->format('G');
                if (isset($hReturns[$hour])) $hReturns[$hour]++;
            });

        // Kunjungan per jam
        Visit::whereDate('visited_at', $dateString)
            ->get()
            ->each(function (Visit $visit) use (&$hVisits, $tz) {
                $hour = (int) Carbon::parse($visit->visited_at)->setTimezone($tz)->format('G');
                if (isset($hVisits[$hour])) $hVisits[$hour]++;
            });

        $chartDaily = [
            'labels'  => $hLabels,
            'loans'   => $hLoans,
            'returns' => $hReturns,
            'visits'  => $hVisits,
        ];

        // ===== CHART MINGGUAN =====
        $weekStart = $currentDate->copy()->startOfWeek(); // Senin
        $weekEnd   = $currentDate->copy()->endOfWeek();

        $wLabels = [];
        $wLoans = $wReturns = $wVisits = [];

        foreach (CarbonPeriod::create($weekStart, $weekEnd) as $d) {
            $dStr      = $d->toDateString();
            $wLabels[] = $d->isoFormat('dd'); // Mo, Tu, ...
            $wLoans[]   = Loan::whereDate('created_at', $dStr)->count();
            $wReturns[] = Loan::whereNotNull('returned_at')->whereDate('returned_at', $dStr)->count();
            $wVisits[]  = Visit::whereDate('visited_at', $dStr)->count();
        }

        $chartWeekly = [
            'labels'  => $wLabels,
            'loans'   => $wLoans,
            'returns' => $wReturns,
            'visits'  => $wVisits,
        ];

        // ===== CHART BULANAN =====
        if ($request->filled('month')) {
            // format: YYYY-MM
            $monthDate = Carbon::createFromFormat('Y-m', $request->month, $tz)->startOfMonth();
        } else {
            $monthDate = $currentDate->copy()->startOfMonth();
        }

        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd   = $monthDate->copy()->endOfMonth();

        $mLabels = [];
        $mLoans = $mReturns = $mVisits = [];

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $d) {
            $dStr      = $d->toDateString();
            $mLabels[] = $d->format('j'); // 1,2,3...
            $mLoans[]   = Loan::whereDate('created_at', $dStr)->count();
            $mReturns[] = Loan::whereNotNull('returned_at')->whereDate('returned_at', $dStr)->count();
            $mVisits[]  = Visit::whereDate('visited_at', $dStr)->count();
        }

        $chartMonthly = [
            'labels'  => $mLabels,
            'loans'   => $mLoans,
            'returns' => $mReturns,
            'visits'  => $mVisits,
        ];

        return view('admin.dashboard', [
            'totalBooks'            => $totalBooks,
            'activeStudents'        => $activeStudents,
            'onLoan'                => $onLoan,
            'visitsToday'           => $visitsToday,
            'visitsTodayClass'      => $visitsTodayClass,
            'visitsTodayIndividual' => $visitsTodayIndividual,
            'chartDaily'            => $chartDaily,
            'chartWeekly'           => $chartWeekly,
            'chartMonthly'          => $chartMonthly,
            'currentDate'           => $currentDate,
        ]);
    }
}
