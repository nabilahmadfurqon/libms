<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return match ($user->role) {
            'petugas'    => redirect()->route('admin.dashboard'),
            'pengunjung' => redirect()->route('pengunjung.dashboard'),
            default      => abort(403, 'Role tidak dikenali.'),
        };
    }
}
