<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // kalau nanti ada BooksSeeder, StudentsSeeder, dsb, taruh di sini
        ]);
    }
}
