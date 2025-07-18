<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Factory;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:factory']);
    }

    public function index()
    {
        $suppliers = User::where('role', 'supplier')
            ->where('approved', true)
            ->paginate(10);
        return view('factory.suppliers.index', [
            'suppliers' => $suppliers,
            'recentOrders' => collect(),
        ]);
    }

    public function show(User $supplier)
    {
        $factory = Auth::user()->factory;
        
        // Verify the supplier is associated with this factory
        if (!$factory->suppliers->contains($supplier)) {
            return redirect()->route('factory.suppliers.index')
                ->with('error', 'You do not have permission to view this supplier.');
        }
        
        // Get supplier's products with inventory status
        $products = $supplier->products()
            ->with(['category', 'inventory'])
            ->orderBy('name')
            ->get();
            
        // Get orders summary
        $orders = Order::where('supplier_id', $supplier->id)
            ->where('factory_id', $factory->id)
            ->with(['items', 'statusHistory'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Get order statistics
        $orderStats = [
            'total' => Order::where('supplier_id', $supplier->id)
                ->where('factory_id', $factory->id)
                ->count(),
            'pending' => Order::where('supplier_id', $supplier->id)
                ->where('factory_id', $factory->id)
                ->where('status', 'pending')
                ->count(),
            'completed' => Order::where('supplier_id', $supplier->id)
                ->where('factory_id', $factory->id)
                ->where('status', 'completed')
                ->count(),
            'cancelled' => Order::where('supplier_id', $supplier->id)
                ->where('factory_id', $factory->id)
                ->where('status', 'cancelled')
                ->count(),
        ];
        
        return view('factory.suppliers.show', [
            'supplier' => $supplier,
            'products' => $products,
            'orders' => $orders,
            'orderStats' => $orderStats
        ]);
    }

    public function create()
    {
        return view('factory.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'products' => 'array',
            'products.*.name' => 'required|string|max:255',
            'products.*.description' => 'required|string',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quality_grade' => 'required|string',
        ]);

        $factory = Auth::user()->factory;
        
        // Create supplier user
        $supplier = User::create([
            'name' => $validated['name'],
            'email' => $validated['contact_email'],
            'password' => bcrypt($validated['contact_phone']), // Using phone as password for now
            'role' => 'supplier',
            'contact_person' => $validated['contact_person'],
            'contact_phone' => $validated['contact_phone'],
            'address' => $validated['address'],
            'approved' => true // Auto-approve since this is a factory-added supplier
        ]);

        // Create products for the supplier
        foreach ($validated['products'] as $productData) {
            Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'supplier_id' => $supplier->id,
                'factory_id' => $factory->id,
                'price' => $productData['price'],
                'quality_grade' => $productData['quality_grade'],
                'type' => 'raw',
                'status' => true
            ]);
        }

        // Add supplier to factory's list
        $factory->suppliers()->attach($supplier);

        return redirect()->route('factory.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function edit(User $supplier)
    {
        return view('factory.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, User $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('factory.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(User $supplier)
    {
        $factory = Auth::user()->factory;
        
        // Remove supplier from factory's list
        $factory->suppliers()->detach($supplier);
        
        // Soft delete the supplier
        $supplier->delete();

        return redirect()->route('factory.suppliers.index')
            ->with('success', 'Supplier removed successfully.');
    }
}
