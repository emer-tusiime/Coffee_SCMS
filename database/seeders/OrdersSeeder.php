<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $orders = [
            [
                'customer_id' => 1,
                'status' => 'delivered',
                'order_date' => now()->subDays(5),
                'total_amount' => 500000
            ],
            [
                'customer_id' => 2,
                'status' => 'processing',
                'order_date' => now()->subDays(2),
                'total_amount' => 1200000
            ],
            [
                'customer_id' => 3,
                'status' => 'pending',
                'order_date' => now(),
                'total_amount' => 900000
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
