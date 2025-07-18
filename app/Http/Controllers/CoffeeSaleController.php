<?php

namespace App\Http\Controllers;

use App\Models\CoffeeSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoffeeSaleController extends Controller
{
    public function index()
    {
        // Get all coffee sales with their status
        $coffeeSales = CoffeeSale::with(['supplier', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('coffee-sales.index', [
            'coffeeSales' => $coffeeSales
        ]);
    }

    public function create()
    {
        return view('coffee-sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0',
            'total_value' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $coffeeSale = CoffeeSale::create([
            'supplier_id' => $validated['supplier_id'],
            'quantity' => $validated['quantity'],
            'total_value' => $validated['total_value'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'created_by' => Auth::id()
        ]);

        return redirect()->route('coffee-sales.index')
            ->with('success', 'Coffee sale recorded successfully.');
    }

    public function show(CoffeeSale $coffeeSale)
    {
        return view('coffee-sales.show', [
            'coffeeSale' => $coffeeSale
        ]);
    }

    public function edit(CoffeeSale $coffeeSale)
    {
        return view('coffee-sales.edit', [
            'coffeeSale' => $coffeeSale
        ]);
    }

    public function update(Request $request, CoffeeSale $coffeeSale)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'total_value' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $coffeeSale->update($validated);

        return redirect()->route('coffee-sales.index')
            ->with('success', 'Coffee sale updated successfully.');
    }

    public function destroy(CoffeeSale $coffeeSale)
    {
        $coffeeSale->delete();

        return redirect()->route('coffee-sales.index')
            ->with('success', 'Coffee sale deleted successfully.');
    }

    public function approve(CoffeeSale $coffeeSale)
    {
        DB::transaction(function () use ($coffeeSale) {
            $coffeeSale->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);
        });

        return redirect()->route('coffee-sales.index')
            ->with('success', 'Coffee sale approved successfully.');
    }

    public function reject(CoffeeSale $coffeeSale)
    {
        DB::transaction(function () use ($coffeeSale) {
            $coffeeSale->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejected_by' => Auth::id()
            ]);
        });

        return redirect()->route('coffee-sales.index')
            ->with('success', 'Coffee sale rejected successfully.');
    }
}
