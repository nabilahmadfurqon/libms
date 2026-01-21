<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Proses autentikasi sesuai LoginRequest
        $request->authenticate();

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Setelah login → selalu diarahkan ke /dashboard
        // /dashboard akan ditangani oleh HomeDashboardController
        return redirect()->intended('/dashboard');
    }

    /**
     * Logout (dipakai oleh petugas; pengunjung pakai controller khusus).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
