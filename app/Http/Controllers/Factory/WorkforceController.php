<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Workforce;
use App\Models\Location;
use App\Models\ProductionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkforceController extends Controller
{
    public function index()
    {
        $workforce = Workforce::with('location', 'productionLine')
            ->get()
            ->groupBy('location_id');
        
        return view('factory.workforce.index', compact('workforce'));
    }

    public function create()
    {
        $locations = Location::all();
        $productionLines = ProductionLine::all();
        return view('factory.workforce.create', compact('locations', 'productionLines'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'production_line_id' => 'nullable|exists:production_lines,id',
            'shift_start' => 'required|date_format:H:i',
            'shift_end' => 'required|date_format:H:i|after:shift_start',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Workforce::create($request->all());
        return redirect()->route('factory.workforce.index')
            ->with('success', 'Workforce member added successfully');
    }

    public function edit(Workforce $workforce)
    {
        $locations = Location::all();
        $productionLines = ProductionLine::all();
        return view('factory.workforce.edit', compact('workforce', 'locations', 'productionLines'));
    }

    public function update(Request $request, Workforce $workforce)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'production_line_id' => 'nullable|exists:production_lines,id',
            'shift_start' => 'required|date_format:H:i',
            'shift_end' => 'required|date_format:H:i|after:shift_start',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $workforce->update($request->all());
        
        // Broadcast the update
        broadcast(new \App\Events\ShiftChange($workforce))->toOthers();

        return redirect()->route('factory.workforce.index')
            ->with('success', 'Workforce member updated successfully');
    }

    public function destroy(Workforce $workforce)
    {
        $workforce->delete();
        
        // Broadcast the deletion
        broadcast(new \App\Events\ShiftChange($workforce))->toOthers();

        return redirect()->route('factory.workforce.index')
            ->with('success', 'Workforce member removed successfully');
    }

    public function getWorkforceStatus()
    {
        $workforce = Workforce::with('location', 'productionLine')
            ->get()
            ->groupBy('location_id');
        
        return response()->json($workforce);
    }

    public function getShiftAvailability($locationId, $date)
    {
        $workforce = Workforce::where('location_id', $locationId)
            ->with('productionLine')
            ->get();
        
        return response()->json($workforce);
    }
}