<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductionLine;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display production efficiency dashboard
     */
    public function efficiency()
    {
        $productionLines = ProductionLine::all();

        // Calculate overall efficiency
        $overallEfficiency = $productionLines->avg('efficiency') ?? 0;

        // Get production lines with low efficiency
        $lowEfficiencyLines = $productionLines->where('efficiency', '<', 80);

        // Get production statistics
        $stats = [
            'total_lines' => $productionLines->count(),
            'active_lines' => $productionLines->where('status', 'active')->count(),
            'maintenance_lines' => $productionLines->where('status', 'maintenance')->count(),
            'inactive_lines' => $productionLines->where('status', 'inactive')->count(),
        ];

        return view('admin.production.efficiency', compact(
            'productionLines',
            'overallEfficiency',
            'lowEfficiencyLines',
            'stats'
        ));
    }

    /**
     * Update production line efficiency
     */
    public function updateEfficiency(Request $request, ProductionLine $productionLine)
    {
        $validated = $request->validate([
            'efficiency' => 'required|numeric|min:0|max:100'
        ]);

        $productionLine->update($validated);

        return redirect()->back()->with('success', 'Production line efficiency updated successfully.');
    }
}
