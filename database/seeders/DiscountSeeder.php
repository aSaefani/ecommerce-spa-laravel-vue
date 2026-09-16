<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        Discount::create([
            'code' => 'HEMAT10',
            'type' => 'percentage',
            'value' => 10,
            'min_order' => 50000,
            'max_uses' => 100,
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
        ]);

        Discount::create([
            'code' => 'POTONG20RB',
            'type' => 'fixed',
            'value' => 20000,
            'min_order' => 100000,
            'max_uses' => 50,
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
        ]);
    }
}
