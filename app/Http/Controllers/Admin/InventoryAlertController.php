<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryAlertController extends Controller
{
    public function index()
    {
        $lowStockProducts = Product::where('stock', '<', 10)->get();

        return view('admin.inventory.alerts', [
            'lowStockProducts' => $lowStockProducts
        ]);
    }
}
