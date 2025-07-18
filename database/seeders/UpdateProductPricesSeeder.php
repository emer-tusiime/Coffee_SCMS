<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateProductPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set a default price (e.g., 10000 UGX) for all products with price 0 or null
        DB::table('products')->whereNull('price')->orWhere('price', 0)->update(['price' => 10000]);
    }
} 