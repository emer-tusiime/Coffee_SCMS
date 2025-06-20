<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('location')->get();
        return view('factory.inventory.index', compact('inventories'));
    }

    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inventory = Inventory::updateOrCreate(
            [
                'location_id' => $request->location_id,
                'product_id' => $request->product_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        // Broadcast the update
        broadcast(new \App\Events\StockUpdate($inventory))->toOthers();

        return redirect()->route('factory.inventory.index')
            ->with('success', 'Inventory updated successfully');
    }

    public function getInventoryStatus()
    {
        $inventories = Inventory::with('location', 'product')
            ->get()
            ->groupBy('location_id');

        return response()->json($inventories);
    }

    public function getLowStockAlerts()
    {
        $alerts = Inventory::where('quantity', '<=', 10)
            ->with('product', 'location')
            ->get();

        return response()->json($alerts);
    }
}
