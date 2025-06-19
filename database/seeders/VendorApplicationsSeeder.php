<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VendorApplication;

class VendorApplicationsSeeder extends Seeder
{
    public function run()
    {
        $applications = [
            [
                'supplier_id' => 1,
                'pdf_path' => 'uploads/vendor_docs/supplier1.pdf',
                'status' => 'approved',
                'submission_date' => now()->subDays(10),
            ],
            [
                'supplier_id' => 2,
                'pdf_path' => 'uploads/vendor_docs/supplier2.pdf',
                'status' => 'pending',
                'submission_date' => now()->subDays(5),
            ],
            [
                'supplier_id' => 3,
                'pdf_path' => 'uploads/vendor_docs/supplier3.pdf',
                'status' => 'rejected',
                'submission_date' => now()->subDays(3),
            ],
        ];

        foreach ($applications as $application) {
            VendorApplication::create($application);
        }
    }
}