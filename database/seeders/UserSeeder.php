<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Admin',
                'username' => 'admin', // 'username' is unique, so you can't use 'email' as 'username
                'email' => 'admin@admin.com',
                'phone' => '081234567890',
                'password' => bcrypt('password'),
                'address' => 'Jl. Raya Bogo No. 1',
            ],
            [
                'name' => 'User',
                'username' => 'user',
                'email' => 'user@user.com',
                'phone' => '081234567891',
                'password' => bcrypt('password'),
                'address' => 'Jl. Raya Bogo No. 2',
            ],
        ];

        foreach ($data as $user) {
            \App\Models\User::create($user);
        }
    }
}
