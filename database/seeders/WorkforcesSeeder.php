<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workforce;

class WorkforcesSeeder extends Seeder
{
    public function run()
    {
        $workforces = [
            [
                'name' => 'John Worker',
                'role' => 'Warehouse Staff',
                'supply_center_id' => 2,
                'shift_times' => '08:00-16:00',
            ],
            [
                'name' => 'Alice Shiftlead',
                'role' => 'Shift Supervisor',
                'supply_center_id' => 1,
                'shift_times' => '09:00-17:00',
            ],
            [
                'name' => 'Samuel Retail',
                'role' => 'Retail Assistant',
                'supply_center_id' => 3,
                'shift_times' => '10:00-18:00',
            ],
        ];

        foreach ($workforces as $workforce) {
            Workforce::create($workforce);
        }
    }
}