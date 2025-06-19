<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerSegment;

class CustomerSegmentsSeeder extends Seeder
{
    public function run()
    {
        $segments = [
            ['name' => 'Wholesale', 'description' => 'Bulk buyers such as supermarkets and large distributors.'],
            ['name' => 'Retail', 'description' => 'Small stores and cafes.'],
            ['name' => 'Individual', 'description' => 'End customers purchasing for personal use.'],
        ];
        foreach ($segments as $segment) {
            CustomerSegment::create($segment);
        }
    }
}