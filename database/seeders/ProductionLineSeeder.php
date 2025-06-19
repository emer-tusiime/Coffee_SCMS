<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductionLine;
use Carbon\Carbon;

class ProductionLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productionLines = [
            [
                'name' => 'Line A - Roasting',
                'description' => 'Main coffee roasting line',
                'status' => 'active',
                'capacity' => 1000,
                'current_batch' => 'BATCH-A001',
                'maintenance_status' => 'up_to_date',
                'last_maintenance_date' => Carbon::now()->subDays(15),
                'next_maintenance_date' => Carbon::now()->addDays(15),
            ],
            [
                'name' => 'Line B - Grinding',
                'description' => 'Primary grinding line',
                'status' => 'active',
                'capacity' => 1200,
                'current_batch' => 'BATCH-B001',
                'maintenance_status' => 'due_soon',
                'last_maintenance_date' => Carbon::now()->subDays(25),
                'next_maintenance_date' => Carbon::now()->addDays(5),
            ],
            [
                'name' => 'Line C - Packaging',
                'description' => 'Automated packaging line',
                'status' => 'maintenance',
                'capacity' => 800,
                'current_batch' => null,
                'maintenance_status' => 'in_progress',
                'last_maintenance_date' => Carbon::now()->subDays(30),
                'next_maintenance_date' => Carbon::now()->addHours(24),
            ],
            [
                'name' => 'Line D - Quality Control',
                'description' => 'Quality control and testing line',
                'status' => 'inactive',
                'capacity' => 500,
                'current_batch' => null,
                'maintenance_status' => 'overdue',
                'last_maintenance_date' => Carbon::now()->subDays(45),
                'next_maintenance_date' => Carbon::now()->subDays(15),
            ],
        ];

        foreach ($productionLines as $line) {
            ProductionLine::create($line);
        }
    }
}
