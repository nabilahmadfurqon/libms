<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk user.
     */
    public function run(): void
    {
        // 1. Akun PETUGAS (punya akses penuh: dashboard admin, master data, dll)
        User::updateOrCreate(
            ['email' => 'petugas@libms.local'],
            [
                'name'     => 'Petugas Perpustakaan',
                'password' => Hash::make('password'), // ganti kalau mau
                'role'     => 'petugas',
            ]
        );

        // 2. Akun PENGUNJUNG (mode kiosk, hanya visit/sirkulasi sesuai role)
        User::updateOrCreate(
            ['email' => 'pengunjung@libms.local'],
            [
                'name'     => 'Kiosk Pengunjung',
                'password' => Hash::make('password'), // sama: "password"
                'role'     => 'pengunjung',
            ]
        );
    }
}
