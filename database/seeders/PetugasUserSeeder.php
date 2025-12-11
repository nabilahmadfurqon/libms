<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PetugasUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'petugas@libms.local'],
            [
                'name' => 'Petugas Perpustakaan',
                'password' => Hash::make('petugas12345'),
                'role' => User::ROLE_PETUGAS,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
