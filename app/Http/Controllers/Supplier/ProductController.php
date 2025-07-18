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
        $user = Auth::user();
        $products = \App\Models\Product::where('supplier_id', $user->id)->get();
        return view('supplier.products.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('supplier.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);
        $validated['supplier_id'] = Auth::id();
        Product::create($validated);
        return redirect()->route('supplier.dashboard')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('supplier.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);
        $product->update($validated);
        return redirect()->route('supplier.dashboard')->with('success', 'Product updated successfully!');
    }

    public function updateStock(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);
        $product->update(['stock' => $validated['stock']]);
        return redirect()->route('supplier.dashboard')->with('success', 'Stock updated successfully!');
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
