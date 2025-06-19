<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            ['name' => 'Central Factory', 'address' => 'Kampala Industrial Area', 'type' => 'factory'],
            ['name' => 'Main Warehouse', 'address' => 'Nalukolongo', 'type' => 'warehouse'],
            ['name' => 'City Retail Store', 'address' => 'Kampala Road', 'type' => 'retailstore'],
        ];
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}