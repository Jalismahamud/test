<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        Business::create([
            'name'     => 'Demo Store',
            'slug'     => 'demo-store',
            'phone'    => '01700000000',
            'email'    => 'demo@store.com',
            'address'  => 'Dhaka, Bangladesh',
            'currency' => 'BDT',
            'tax_rate' => 15,
            'timezone' => 'Asia/Dhaka',
        ]);
    }
}
