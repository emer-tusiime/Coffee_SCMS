<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Models\CoffeeSale;
use Illuminate\Http\Request;

class CoffeeSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    public function index()
    {
        $sales = CoffeeSale::where('supplier_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalQuantity = CoffeeSale::where('supplier_id', auth()->id())
            ->sum('quantity');

        $totalValue = CoffeeSale::where('supplier_id', auth()->id())
            ->sum('total_value');

        return view('supplier.coffee-sales.index', [
            'sales' => $sales,
            'totalQuantity' => $totalQuantity,
            'totalValue' => $totalValue
        ]);
    }

    public function create()
    {
        return view('supplier.coffee-sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
            'quality_grade' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        CoffeeSale::create([
            'supplier_id' => auth()->id(),
            'quantity' => $validated['quantity'],
            'price_per_kg' => $validated['price_per_kg'],
            'quality_grade' => $validated['quality_grade'],
            'notes' => $validated['notes'] ?? null,
            'approved' => false
        ]);

        return redirect()->route('supplier.coffee-sales.index')
            ->with('success', 'Coffee sale reported successfully. Awaiting factory approval.');
    }
}
