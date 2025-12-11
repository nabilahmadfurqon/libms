<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@libms.local'],
            [
                'name' => 'Administrator LibMS',
                'password' => Hash::make('admin12345'),
                'role' => User::ROLE_ADMIN,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
