<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Arabica Classic',
                'description' => 'High-quality Arabica beans.',
                'sku' => 'ARABICA-001',
                'price' => 25000,
                'category_id' => 1,
                'supplier_id' => 1
            ],
            [
                'name' => 'Robusta Rich',
                'description' => 'Strong Robusta blend.',
                'sku' => 'ROBUSTA-002',
                'price' => 20000,
                'category_id' => 2,
                'supplier_id' => 2
            ],
            [
                'name' => 'Instant Gold',
                'description' => 'Premium instant coffee.',
                'sku' => 'INSTANT-003',
                'price' => 18000,
                'category_id' => 3,
                'supplier_id' => 3
            ],
        ];
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}