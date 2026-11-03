<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin',        'email' => 'admin@example.com',     'role' => 'admin'],
            ['name' => 'Coach Uji',    'email' => 'coach@example.com',     'role' => 'coach'],
            ['name' => 'Coach Dua',    'email' => 'coach2@example.com',    'role' => 'coach'],
            ['name' => 'Mahasiswa Uji', 'email' => 'mahasiswa@example.com', 'role' => 'mahasiswa', 'nim' => '2024001'],
        ];

        foreach ($accounts as $account) {
            User::create($account + ['password' => 'password123']);
        }
    }
}