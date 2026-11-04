<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Admin SIMHOP',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'nim' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Coach Uji',
                'email' => 'coach@example.com',
                'role' => 'coach',
                'nim' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Coach Dua',
                'email' => 'coach2@example.com',
                'role' => 'coach',
                'nim' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Mahasiswa Uji',
                'email' => 'mahasiswa@example.com',
                'role' => 'mahasiswa',
                'nim' => '2024001',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::where('email', $account['email'])->first();

            if (! $user) {
                User::create(array_merge($account, [
                    'password' => Hash::make('password123'),
                ]));
            } else {
                $user->update([
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'nim' => $account['nim'],
                    'is_active' => $account['is_active'],
                ]);
            }
        }
    }
}
