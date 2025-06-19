<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportsSeeder extends Seeder
{
    public function run()
    {
        $reports = [
            [
                'stakeholder_id' => 1,
                'report_type' => 'Inventory',
                'generated_date' => now()->subDays(1),
                'content' => 'Inventory levels are optimal for all products.',
            ],
            [
                'stakeholder_id' => 2,
                'report_type' => 'Sales',
                'generated_date' => now()->subDays(2),
                'content' => 'Sales increased by 15% compared to last month.',
            ],
            [
                'stakeholder_id' => 3,
                'report_type' => 'Order',
                'generated_date' => now()->subDays(3),
                'content' => 'Three orders pending delivery.',
            ],
        ];

        foreach ($reports as $report) {
            Report::create($report);
        }
    }
}