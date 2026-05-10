<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => '管理者ユーザー',
                'role'     => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'guest@example.com'],
            [
                'name'     => '宿泊者ユーザー',
                'role'     => 'guest',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
