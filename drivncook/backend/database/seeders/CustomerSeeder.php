<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
    Customer::firstOrCreate(
        ['email' => 'client@drivncook.test'], 
        [
            'name' => 'Client Test',
            'phone' => '0612345678',
            'password' => Hash::make('client123'),
        ]
    );
    }
}
