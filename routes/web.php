<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\CirculationController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\Petugas\PetugasDashboardController;
use App\Http\Controllers\BarcodeController;

Route::get('/', fn () => redirect()->route('login'));

// SEMUA ROUTE YANG BUTUH LOGIN
Route::middleware(['auth'])->group(function () {

    // ---------- ADMIN ----------
    Route::middleware('role:admin')->group(function () {

        // Dashboard Admin (DashboardController pakai __invoke)
        Route::get('/admin/dashboard', DashboardController::class)
            ->name('admin.dashboard');

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

        // ✅ Export data siswa (pakai filter q juga nanti di controller)
        Route::get('/admin/students/export', [StudentController::class, 'export'])
            ->name('admin.students.export');

        // ---------- BARCODE (khusus admin) ----------
        Route::get('/admin/barcodes/books', [BarcodeController::class, 'books'])
            ->name('admin.barcodes.books');

        Route::get('/admin/barcodes/students', [BarcodeController::class, 'students'])
            ->name('admin.barcodes.students');

        Route::post('/admin/barcodes/books/preview', [BarcodeController::class, 'booksPreview'])
            ->name('admin.barcodes.books.preview');

        Route::post('/admin/barcodes/students/preview', [BarcodeController::class, 'studentsPreview'])
            ->name('admin.barcodes.students.preview');
    });

    // ---------- PETUGAS ----------
    Route::middleware('role:petugas')->group(function () {

        // Dashboard petugas
        Route::get('/petugas/dashboard', PetugasDashboardController::class)
            ->name('petugas.dashboard');

        // ---------- SIRKULASI ----------
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

        // ---------- KUNJUNGAN ----------
        Route::prefix('visits')->group(function () {
            Route::get('/', [VisitController::class, 'index'])
                ->name('visits.index');

            Route::post('/', [VisitController::class, 'store'])
                ->name('visits.store');

            Route::get('/history', [VisitController::class, 'history'])
                ->name('visits.history');
        });
    });

    // ---------- SHARED ROUTES (Admin & Petugas) ----------
    Route::middleware('role:admin,petugas')->group(function () {
        // Tambahkan route gabungan di sini kalau nanti ada
    });
});

// ROUTE AUTH (login/logout) DIAMBIL DARI FILE auth.php
require __DIR__ . '/auth.php';
