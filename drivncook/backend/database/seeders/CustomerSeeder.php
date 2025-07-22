<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'name' => 'Client Test',
            'email' => 'client@drivncook.test',
            'phone' => '0612345678',
            'password' => Hash::make('client123'),
        ]);
    }
}
