<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
| Versi simpel: hanya login, logout, register (opsional), dan reset password.
| TIDAK ada email verification, jadi tidak ada EmailVerificationPromptController.
*/

// ========== GUEST (belum login) ==========
Route::middleware('guest')->group(function () {
    // LOGIN
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

}); 

// ========== AUTH (sudah login) ==========
Route::middleware('auth')->group(function () {
    // LOGOUT
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
