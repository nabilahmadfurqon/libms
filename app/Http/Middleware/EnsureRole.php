<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Kalau tidak ada parameter role, lewat saja
        if (empty($roles)) {
            return $next($request);
        }

        // Cek role user
        if (! in_array($user->role, $roles, true)) {
            // Pilihan A (lebih tegas):
            abort(403, 'Akses ditolak.');

            // Pilihan B (lebih halus):
            // return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
