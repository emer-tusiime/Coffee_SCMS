<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class InventoriesSeeder extends Seeder
{
    public function run()
    {
        $inventories = [
            ['product_id' => 1, 'quantity' => 150, 'location_id' => 1],
            ['product_id' => 2, 'quantity' => 120, 'location_id' => 2],
            ['product_id' => 3, 'quantity' => 80, 'location_id' => 3],
        ];
        foreach ($inventories as $inventory) {
            Inventory::create($inventory);
        }
    }
}