<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WholesalerProductPrice;

class UpdateRetailerOrderItemPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-retailer-item-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all retailer order items to use the wholesaler price if available.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('order_type', 'retailer')->with(['items', 'wholesaler'])->get();
        $updated = 0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $wholesalerId = $order->wholesaler_id;
                $wholesalerPrice = WholesalerProductPrice::where('wholesaler_id', $wholesalerId)
                    ->where('product_id', $item->product_id)
                    ->value('price');
                if ($wholesalerPrice && $item->price != $wholesalerPrice) {
                    $item->price = $wholesalerPrice;
                    $item->save();
                    $updated++;
                }
            }
        }
        $this->info("Updated $updated order item prices to wholesaler prices.");
    }
}
