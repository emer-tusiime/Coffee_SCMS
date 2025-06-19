<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomersSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'name' => 'Cafe Java',
                'contact_info' => 'contact@cafejava.com, +256703456789',
                'segment_id' => 2
            ],
            [
                'name' => 'SuperMart Wholesalers',
                'contact_info' => 'info@supermart.com, +256704567890',
                'segment_id' => 1
            ],
            [
                'name' => 'Jane Doe',
                'contact_info' => 'jane.doe@gmail.com, +256705678901',
                'segment_id' => 3
            ],
        ];
        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}