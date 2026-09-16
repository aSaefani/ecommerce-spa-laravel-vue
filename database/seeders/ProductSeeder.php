<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_id' => 1, 'name' => 'Laptop Dell', 'description' => 'Laptop gaming performa tinggi', 'price' => 15000000, 'stock' => 10],
            ['category_id' => 1, 'name' => 'Mouse Wireless', 'description' => 'Mouse nirkabel ergonomis', 'price' => 250000, 'stock' => 50],
            ['category_id' => 2, 'name' => 'Kaos Polos', 'description' => 'Kaos katun premium', 'price' => 75000, 'stock' => 100],
            ['category_id' => 2, 'name' => 'Celana Jeans', 'description' => 'Celana jeans biru klasik', 'price' => 150000, 'stock' => 80],
            ['category_id' => 3, 'name' => 'Gelas Set', 'description' => 'Set gelas minum 6 pcs', 'price' => 120000, 'stock' => 30],
            ['category_id' => 4, 'name' => 'Kopi Premium', 'description' => 'Kopi arabika pilihan 500gr', 'price' => 85000, 'stock' => 200],
            ['category_id' => 5, 'name' => 'Yoga Mat', 'description' => 'Matras yoga anti slip', 'price' => 180000, 'stock' => 25],
        ];

        foreach ($products as $prod) {
            $product = Product::create($prod);
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $prod['stock'],
                'description' => 'Stok awal',
            ]);
        }
    }
}
