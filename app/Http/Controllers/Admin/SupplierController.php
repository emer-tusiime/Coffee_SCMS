<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Services\Analytics\SupplierAnalyticsService;

class SupplierController extends Controller
{
    protected $supplierAnalyticsService;

    public function __construct(SupplierAnalyticsService $supplierAnalyticsService)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->supplierAnalyticsService = $supplierAnalyticsService;
    }

    /**
     * Display a listing of the suppliers.
     */
    public function index()
    {
        $suppliers = Supplier::select('id', 'name', 'contact_info', 'status', 'created_at')
            ->get()
            ->map(function ($supplier) {
                $performance = $this->supplierAnalyticsService->getPerformanceMetrics($supplier->id);
                $supplier->delivery_rate = $performance['delivery_rate'];
                $supplier->quality_score = $performance['quality_score'];
                $supplier->performance_status = $performance['status'];
                return $supplier;
            });

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'required|string',
            'status' => 'required|in:pending,approved,suspended'
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        $performance = $this->supplierAnalyticsService->getPerformanceMetrics($supplier->id);
        $supplier->delivery_rate = $performance['delivery_rate'];
        $supplier->quality_score = $performance['quality_score'];
        $supplier->performance_status = $performance['status'];

        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'required|string',
            'status' => 'required|in:pending,approved,suspended'
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
