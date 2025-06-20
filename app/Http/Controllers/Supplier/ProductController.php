<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $supplier = Auth::user()->supplier;
        $products = $supplier->products;
        return view('supplier.products.index', compact('products'));
    }

    public function create()
    {
        return view('supplier.products.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $supplier = Auth::user()->supplier;
        $product = $supplier->products()->create($request->all());

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        return view('supplier.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product->update($request->all());
        
        // Broadcast the update
        broadcast(new \App\Events\ProductUpdate($product))->toOthers();

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->supplier_id !== Auth::user()->supplier->id) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $product->delete();
        
        // Broadcast the deletion
        broadcast(new \App\Events\ProductUpdate($product))->toOthers();

        return redirect()->route('supplier.products.index')
            ->with('success', 'Product deleted successfully');
    }
}
