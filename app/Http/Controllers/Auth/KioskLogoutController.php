<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KioskLogoutController extends Controller
{
    /**
     * Form konfirmasi logout kiosk (input password akun yang sedang login).
     */
    public function show()
    {
        return view('auth.kiosk-logout');
    }

    /**
     * Proses logout kiosk: cek password dulu.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Pastikan ini hanya dipakai oleh role pengunjung
        if ($user->role !== 'pengunjung') {
            abort(403, 'Hanya untuk akun pengunjung.');
        }

        // Cek password akun pengunjung (yang diketahui guru)
        if (! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['password' => 'Password salah.'])
                ->withInput();
        }

        // Logout standar
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kiosk berhasil di-logout.');
    }
}
