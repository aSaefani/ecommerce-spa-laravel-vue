<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecommerce.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@ecommerce.local',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'customer@ecommerce.local',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);
    }
}
