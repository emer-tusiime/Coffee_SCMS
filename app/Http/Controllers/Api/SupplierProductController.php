<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierProductController extends Controller
{
    public function getProducts($supplierId)
    {
        $factory = Auth::user()->factory;
        
        $products = Product::where('supplier_id', $supplierId)
            ->where('factory_id', $factory->id)
            ->where('type', 'raw')
            ->where('status', true)
            ->get();
            
        return response()->json($products);
    }
}
