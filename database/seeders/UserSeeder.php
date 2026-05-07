<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User',    'email' => 'admin@demo.com',   'role' => 'admin'],
            ['name' => 'Manager User',  'email' => 'manager@demo.com', 'role' => 'manager'],
            ['name' => 'Cashier User',  'email' => 'cashier@demo.com', 'role' => 'cashier'],
        ];

        foreach ($users as $user) {
            User::create([
                'business_id' => 1,
                'name'        => $user['name'],
                'email'       => $user['email'],
                'password'    => Hash::make('password'),
                'role'        => $user['role'],
                'is_active'   => true,
            ]);
        }
    }
}
