<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Student;
use App\Models\Loan;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    /**
     * Controller ini invokable: route langsung ke __invoke.
     */
    public function __invoke(Request $request)
    {
        $payload = $this->buildPayload($request);

        return view('admin.dashboard', $payload);
    }

    /**
     * Siapkan semua data untuk dashboard admin:
     * - KPI kartu atas
     * - Chart harian / mingguan / bulanan
     */
    private function buildPayload(Request $request): array
    {
        // 1. SETTING ZONA WAKTU & TANGGAL ACUAN
        $appTimezone = config('app.timezone', 'Asia/Jakarta');

        if ($request->filled('start_date')) {
            $currentDate = Carbon::parse($request->start_date)->setTimezone($appTimezone);
        } else {
            $currentDate = Carbon::now($appTimezone);
        }

        $dateString = $currentDate->toDateString();

        // 2. KPI UTAMA
        $totalBooks     = (int) Book::sum('total_copies');
        $activeStudents = (int) Student::count();
        $onLoan         = (int) Loan::whereNull('returned_at')->count();

        // ===== KUNJUNGAN HARI INI =====
        $baseToday = Visit::whereDate('visited_at', $dateString);

        // total semua baris kunjungan
        $visitsToday = (int) (clone $baseToday)->count();

        // kunjungan kelas = punya class_name
        $visitsTodayClass = (int) (clone $baseToday)
            ->whereNotNull('class_name')
            ->count();

        // kunjungan perorangan = semua yang TIDAK punya class_name
        $visitsTodayIndividual = (int) (clone $baseToday)
            ->whereNull('class_name')
            ->count();

        // opsional: breakdown manual vs scan (kalau mau dipakai di tempat lain)
        $manualSingleToday = (int) (clone $baseToday)
            ->whereNull('class_name')        // tetap perorangan
            ->where('purpose', 'kartu_rusak')// input manual karena kartu rusak/lupa
            ->count();

        // scan = perorangan - manual
        $scanVisitsToday = max(0, $visitsTodayIndividual - $manualSingleToday);

        // 3. CHART HARIAN (24 jam)
        $hours   = range(0, 23);
        $hLabels = array_map(fn ($h) => sprintf('%02d:00', $h), $hours);
        $hLoans = $hReturns = $hVisits = array_fill(0, 24, 0);

        // Pinjam per jam
        Loan::whereDate('created_at', $dateString)
            ->get()
            ->each(function (Loan $loan) use (&$hLoans, $appTimezone) {
                $hour = (int) Carbon::parse($loan->created_at)->setTimezone($appTimezone)->format('G');
                if (isset($hLoans[$hour])) {
                    $hLoans[$hour]++;
                }
            });

        // Kembali per jam
        Loan::whereNotNull('returned_at')
            ->whereDate('returned_at', $dateString)
            ->get()
            ->each(function (Loan $loan) use (&$hReturns, $appTimezone) {
                $hour = (int) Carbon::parse($loan->returned_at)->setTimezone($appTimezone)->format('G');
                if (isset($hReturns[$hour])) {
                    $hReturns[$hour]++;
                }
            });

        // Kunjungan per jam
        Visit::whereDate('visited_at', $dateString)
            ->get()
            ->each(function (Visit $visit) use (&$hVisits, $appTimezone) {
                $hour = (int) Carbon::parse($visit->visited_at)->setTimezone($appTimezone)->format('G');
                if (isset($hVisits[$hour])) {
                    $hVisits[$hour]++;
                }
            });

        $chartDaily = [
            'labels'  => $hLabels,
            'loans'   => $hLoans,
            'returns' => $hReturns,
            'visits'  => $hVisits,
        ];

        // 4. CHART MINGGUAN (senin-minggu)
        $weekStart = $currentDate->copy()->startOfWeek(); // default: Monday
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

        // 5. CHART BULANAN (tanggal 1..n)
        $monthStart = $currentDate->copy()->startOfMonth();
        $monthEnd   = $currentDate->copy()->endOfMonth();

        $mLabels = [];
        $mLoans = $mReturns = $mVisits = [];

        foreach (CarbonPeriod::create($monthStart, $monthEnd) as $d) {
            $dStr      = $d->toDateString();
            $mLabels[] = $d->format('j'); // 1, 2, 3...
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

        return [
            'totalBooks'            => $totalBooks,
            'activeStudents'        => $activeStudents,
            'onLoan'                => $onLoan,

            'visitsToday'           => $visitsToday,
            'visitsTodayIndividual' => $visitsTodayIndividual, // ⬅️ dipakai di Blade
            'visitsTodayClass'      => $visitsTodayClass,      // ⬅️ dipakai di Blade

            // opsional, kalau nanti mau dipakai di tempat lain
            'visitsTodayScan'       => $scanVisitsToday,
            'visitsTodayManual'     => $manualSingleToday,

            'chartDaily'            => $chartDaily,
            'chartWeekly'           => $chartWeekly,
            'chartMonthly'          => $chartMonthly,
            'currentDate'           => $currentDate,
        ];
    }
}
