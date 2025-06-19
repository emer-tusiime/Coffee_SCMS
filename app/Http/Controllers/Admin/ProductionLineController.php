<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductionLine;

class ProductionLineController extends Controller
{
    /**
     * ProductionLineController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of production lines.
     */
    public function index()
    {
        $productionLines = ProductionLine::orderBy('created_at', 'desc')->get();
        return view('admin.production.lines.index', compact('productionLines'));
    }

    /**
     * Show the form for creating a new production line.
     */
    public function create()
    {
        return view('admin.production.lines.create');
    }

    /**
     * Store a newly created production line in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        ProductionLine::create($validated);

        return redirect()->route('admin.production.lines.index')
            ->with('success', 'Production line created successfully.');
    }

    /**
     * Display the specified production line.
     */
    public function show(ProductionLine $line)
    {
        return view('admin.production.lines.show', compact('line'));
    }

    /**
     * Show the form for editing the specified production line.
     */
    public function edit(ProductionLine $line)
    {
        return view('admin.production.lines.edit', compact('line'));
    }

    /**
     * Update the specified production line in storage.
     */
    public function update(Request $request, ProductionLine $line)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        $line->update($validated);

        return redirect()->route('admin.production.lines.index')
            ->with('success', 'Production line updated successfully.');
    }

    /**
     * Remove the specified production line from storage.
     */
    public function destroy(ProductionLine $line)
    {
        $line->delete();

        return redirect()->route('admin.production.lines.index')
            ->with('success', 'Production line deleted successfully.');
    }
}
