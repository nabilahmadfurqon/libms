<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ pakai facade
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Jika tidak ada guard yang ditentukan, gunakan guard default (web)
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // ✅ ambil user dari guard yang sama (Intelephense tidak protes)
                $user = Auth::guard($guard)->user();

                $target = match ($user?->role) {
                    'admin'   => route('admin.dashboard'),
                    'petugas' => route('petugas.dashboard'),
                    default   => '/login',
                };

                // RedirectResponse masih kompatibel dengan tipe Response
                return redirect($target);
            }
        }

        return $next($request);
    }
}
