<?php

namespace Database\Seeders;
//use App\Models\Supplier;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            ['name' => 'Kampala Coffee Growers', 'contact_info' => 'kampala@growers.com', 'status' => 'approved'],
            ['name' => 'Mountain Beans Ltd.', 'contact_info' => 'info@mountainbeans.com', 'status' => 'pending'],
            ['name' => 'Western Roasters', 'contact_info' => 'sales@westernroasters.com', 'status' => 'approved'],
        ];
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
