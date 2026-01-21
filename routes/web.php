<?php

use Illuminate\Support\Facades\Route;

// CONTROLLERS
use App\Http\Controllers\HomeDashboardController;
use App\Http\Controllers\Auth\KioskLogoutController;

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\CirculationController;
use App\Http\Controllers\VisitController;
// Kalau masih pakai PetugasDashboard, bisa di-include:
// use App\Http\Controllers\Petugas\PetugasDashboardController;
use App\Http\Controllers\BarcodeController;

// Landing → redirect ke login
Route::get('/', fn () => redirect()->route('login'));

// ========== ROUTE YANG BUTUH LOGIN ==========
Route::middleware(['auth'])->group(function () {

    /**
     * SATU PINTU DASHBOARD
     * Semua user login diarahkan ke /dashboard
     * - petugas    → admin.dashboard
     * - pengunjung → pengunjung.dashboard
     */
    Route::get('/dashboard', HomeDashboardController::class)
        ->name('dashboard');

    // ========== PETUGAS ==========
    // Role ini menggabungkan "admin" + "petugas" lama.
    Route::middleware('role:petugas')->group(function () {

        // DASHBOARD ADMIN (petugas)
        Route::get('/admin/dashboard', AdminDashboardController::class)
            ->name('admin.dashboard');

        // JSON untuk auto-refresh KPI & chart dashboard
        Route::get('/admin/dashboard/data', [AdminDashboardController::class, 'data'])
            ->name('admin.dashboard.data');

        // Manajemen User (CRUD)
        Route::resource('/admin/users', UserController::class)
            ->names('admin.users')
            ->except(['show']);

        // Laporan Admin (Index + Export)
        Route::get('/admin/reports', [ReportController::class, 'index'])
            ->name('admin.reports');
        Route::get('/admin/reports/export', [ReportController::class, 'export'])
            ->name('admin.reports.export');

        // ---------- BOOKS ----------
        Route::resource('/admin/books', BookController::class)
            ->names('admin.books')
            ->except(['show']);

        Route::post('/admin/books/import', [BookController::class, 'import'])
            ->name('admin.books.import');

        Route::get('/admin/books/template', [BookController::class, 'template'])
            ->name('admin.books.template');

        Route::get('/admin/books/sample', [BookController::class, 'sample'])
            ->name('admin.books.sample');

        Route::get('/admin/books/export', [BookController::class, 'export'])
            ->name('admin.books.export');

        // ---------- STUDENTS ----------
        Route::resource('/admin/students', StudentController::class)
            ->names('admin.students')
            ->except(['show']);

        Route::post('/admin/students/import', [StudentController::class, 'import'])
            ->name('admin.students.import');

        Route::get('/admin/students/template', [StudentController::class, 'template'])
            ->name('admin.students.template');

        Route::get('/admin/students/sample', [StudentController::class, 'sample'])
            ->name('admin.students.sample');

        // Export data siswa
        Route::get('/admin/students/export', [StudentController::class, 'export'])
            ->name('admin.students.export');

        // ---------- BARCODE ----------
        Route::get('/admin/barcodes/books', [BarcodeController::class, 'books'])
            ->name('admin.barcodes.books');

        Route::get('/admin/barcodes/students', [BarcodeController::class, 'students'])
            ->name('admin.barcodes.students');

        Route::post('/admin/barcodes/books/preview', [BarcodeController::class, 'booksPreview'])
            ->name('admin.barcodes.books.preview');

        Route::post('/admin/barcodes/students/preview', [BarcodeController::class, 'studentsPreview'])
            ->name('admin.barcodes.students.preview');

        // (Kalau masih ingin Dashboard Petugas terpisah, boleh hidupkan lagi:)
        // Route::get('/petugas/dashboard', PetugasDashboardController::class)
        //     ->name('petugas.dashboard');

        // ---------- SIRKULASI (PETUGAS) ----------
        Route::prefix('circulation')->group(function () {
            Route::get('/', [CirculationController::class, 'index'])
                ->name('circulation.index');

            Route::post('/borrow', [CirculationController::class, 'borrow'])
                ->name('circulation.borrow');

            Route::post('/return', [CirculationController::class, 'return'])
                ->name('circulation.return');

            Route::get('/history', [CirculationController::class, 'history'])
                ->name('circulation.history');
        });

        // ---------- KUNJUNGAN (PETUGAS) ----------
        Route::prefix('visits')->group(function () {
            Route::get('/', [VisitController::class, 'index'])
                ->name('visits.index');

            Route::post('/', [VisitController::class, 'store'])
                ->name('visits.store');

            Route::get('/history', [VisitController::class, 'history'])
                ->name('visits.history');
                Route::post('/class', [VisitController::class, 'storeClass'])
        ->name('visits.store-class');

        });
    });

    // ========== PENGUNJUNG (KIOSK / SISWA) ==========
    Route::middleware('role:pengunjung')->group(function () {

        // Dashboard pengunjung → kita pakai halaman visit scanner
        Route::get('/pengunjung/dashboard', [VisitController::class, 'index'])
            ->name('pengunjung.dashboard');

        // Scan kunjungan mandiri
        Route::post('/pengunjung/visits', [VisitController::class, 'store'])
            ->name('pengunjung.visits.store');

        // (Opsional) kalau mau pengunjung bisa sirkulasi mandiri:
        Route::get('/pengunjung/circulation', [CirculationController::class, 'index'])
            ->name('pengunjung.circulation.index');

        Route::post('/pengunjung/circulation/borrow', [CirculationController::class, 'borrow'])
            ->name('pengunjung.circulation.borrow');

        Route::post('/pengunjung/circulation/return', [CirculationController::class, 'return'])
            ->name('pengunjung.circulation.return');

        // Logout kiosk khusus (pakai password)
        Route::get('/pengunjung/logout', [KioskLogoutController::class, 'show'])
            ->name('pengunjung.logout.show');

        Route::post('/pengunjung/logout', [KioskLogoutController::class, 'confirm'])
            ->name('pengunjung.logout.confirm');
    });
});

// ROUTE AUTH (login/logout) DIAMBIL DARI FILE auth.php
require __DIR__ . '/auth.php';
