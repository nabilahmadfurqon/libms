<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
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
     * Proses login & redirect berdasarkan role.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        $target = match ($user?->role) {
            'admin'   => route('admin.dashboard', absolute: false),
            'petugas' => route('petugas.dashboard', absolute: false),
            default   => '/login',
        };

        return redirect()->intended($target);
    }

    /**
     * Logout.
     */
    public function destroy(): RedirectResponse
    {
        // gunakan helper agar linter tak protes
        $req = request();

        Auth::guard('web')->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect('/'); // kembali ke login
    }
}
