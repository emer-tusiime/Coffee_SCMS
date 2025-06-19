<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;

class OrderItemsSeeder extends Seeder
{
    public function run()
    {
        $orderItems = [
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 10,
                'price' => 25000,
            ],
            [
                'order_id' => 1,
                'product_id' => 2,
                'quantity' => 5,
                'price' => 20000,
            ],
            [
                'order_id' => 2,
                'product_id' => 2,
                'quantity' => 20,
                'price' => 20000,
            ],
            [
                'order_id' => 3,
                'product_id' => 3,
                'quantity' => 2,
                'price' => 18000,
            ],
        ];

        foreach ($orderItems as $item) {
            OrderItem::create($item);
        }
    }
}