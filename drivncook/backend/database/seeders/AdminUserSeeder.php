<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'root',
            'phone' => '0600000000',
            'password' => Hash::make('root'), // mot de passe : azerty123
            'role' => 'admin',
            'franchisee_id' => null,
        ]);
    }
}