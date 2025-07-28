<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin2@drivncook.test'], 
            [
                'name' => 'Admin',
                'phone' => '0600000000',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'franchisee_id' => null,
            ]
        );

    }
}