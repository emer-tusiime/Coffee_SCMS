<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:retailer']);
    }

    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::paginate(10);
        return view('retailer.products.index', compact('products'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('retailer.products.show', compact('product'));
    }
}
