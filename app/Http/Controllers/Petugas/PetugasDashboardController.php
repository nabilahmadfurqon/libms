<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class PetugasDashboardController extends Controller
{
    public function __invoke()
    {
        // Pakai Carbon + timezone app (config('app.timezone'))
        $today = today(); // sama dengan Carbon::today()

        // PINJAM HARI INI
        $loansToday = (int) Loan::whereDate('created_at', $today)->count();
        // Kalau kamu punya kolom loaned_at, lebih bagus:
        // $loansToday = (int) Loan::whereDate('loaned_at', $today)->count();

        // KEMBALI HARI INI
        $returnsToday = (int) Loan::whereNotNull('returned_at')
            ->whereDate('returned_at', $today)
            ->count();

        // KUNJUNGAN HARI INI
        $visitsToday = (int) Visit::whereDate('visited_at', $today)->count();
        // (kalau di tabel kamu belum ada visited_at dan masih pakai created_at,
        //  sementara bisa: whereDate('created_at', $today))

        // TOTAL TERLAMBAT (semua)
        $overdue = (int) Loan::overdue()->count();

        // JATUH TEMPO HARI INI
        $dueToday = (int) Loan::active()
            ->whereDate('due_at', $today)
            ->count();

        // DI DALAM RUANG (opsional)
        // Kalau tabel visits TIDAK punya kolom checkout_at, ambil saja kunjungan hari ini.
        $inRoom = (int) Visit::whereDate('visited_at', $today)->count();

        return view('petugas.dashboard', compact(
            'loansToday',
            'returnsToday',
            'visitsToday',
            'overdue',
            'dueToday',
            'inRoom'
        ));
    }
}
