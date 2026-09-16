<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Produk elektronik dan gadget'],
            ['name' => 'Fashion', 'description' => 'Pakaian dan aksesori'],
            ['name' => 'Rumah Tangga', 'description' => 'Peralatan rumah tangga'],
            ['name' => 'Makanan & Minuman', 'description' => 'Makanan dan minuman berkualitas'],
            ['name' => 'Olahraga', 'description' => 'Peralatan olahraga'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
