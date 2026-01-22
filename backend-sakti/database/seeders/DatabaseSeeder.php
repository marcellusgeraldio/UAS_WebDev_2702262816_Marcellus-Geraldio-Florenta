<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan ada baris ini

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Kita paksa buat user spesifik ini
        User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'test@kampus.ac.id',
            'password' => bcrypt('password123'), // Passwordnya 'password123'
        ]);
    }
}