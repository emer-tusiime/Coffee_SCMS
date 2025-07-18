<?php

namespace App\Http\Controllers\Retailer;

use App\Models\RetailerInventoryAlert;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryAlertController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:retailer']);
    }

    public function index()
    {
        $alerts = RetailerInventoryAlert::where('retailer_id', Auth::id())
            ->with(['product'])
            ->paginate(10);

        $products = Product::where('type', 'processed')
            ->where('status', true)
            ->get();

        return view('retailer.inventory-alerts.index', compact('alerts', 'products'));
    }

    public function create()
    {
        $products = Product::where('type', 'processed')
            ->where('status', true)
            ->get();

        return view('retailer.inventory-alerts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'threshold' => 'required|integer|min:0',
            'alert_frequency' => 'required|in:1,7,30',
            'alert_method' => 'required|in:email,sms,both',
            'notes' => 'nullable|string',
        ]);

        $existingAlert = RetailerInventoryAlert::where('retailer_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingAlert) {
            return redirect()->back()->with('error', 'Alert already exists for this product');
        }

        RetailerInventoryAlert::create([
            'retailer_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'threshold' => $validated['threshold'],
            'alert_frequency' => $validated['alert_frequency'],
            'alert_method' => $validated['alert_method'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true
        ]);

        return redirect()->route('retailer.inventory-alerts.index')
            ->with('success', 'Inventory alert created successfully.');
    }

    public function edit(RetailerInventoryAlert $alert)
    {
        if ($alert->retailer_id !== Auth::id()) {
            return redirect()->route('retailer.inventory-alerts.index')
                ->with('error', 'You cannot edit this alert.');
        }

        $products = Product::where('type', 'processed')
            ->where('status', true)
            ->get();

        return view('retailer.inventory-alerts.edit', compact('alert', 'products'));
    }

    public function update(Request $request, RetailerInventoryAlert $alert)
    {
        if ($alert->retailer_id !== Auth::id()) {
            return redirect()->route('retailer.inventory-alerts.index')
                ->with('error', 'You cannot update this alert.');
        }

        $validated = $request->validate([
            'threshold' => 'required|integer|min:0',
            'alert_frequency' => 'required|in:1,7,30',
            'alert_method' => 'required|in:email,sms,both',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $alert->update($validated);

        return redirect()->route('retailer.inventory-alerts.index')
            ->with('success', 'Inventory alert updated successfully.');
    }

    public function destroy(RetailerInventoryAlert $alert)
    {
        if ($alert->retailer_id !== Auth::id()) {
            return redirect()->route('retailer.inventory-alerts.index')
                ->with('error', 'You cannot delete this alert.');
        }

        $alert->delete();

        return redirect()->route('retailer.inventory-alerts.index')
            ->with('success', 'Inventory alert deleted successfully.');
    }

    public function checkAlerts()
    {
        $alerts = RetailerInventoryAlert::where('retailer_id', Auth::id())
            ->where('is_active', true)
            ->get();

        foreach ($alerts as $alert) {
            $product = $alert->product;
            
            if ($product->current_stock < $alert->threshold) {
                $this->sendAlert($alert, $product);
            }
        }

        return response()->json(['success' => true]);
    }

    private function sendAlert(RetailerInventoryAlert $alert, Product $product)
    {
        $retailer = Auth::user();
        
        if ($alert->alert_method === 'email' || $alert->alert_method === 'both') {
            $retailer->notify(new \App\Notifications\InventoryLowNotification(
                $product, 
                $alert->threshold,
                'email'
            ));
        }

        if ($alert->alert_method === 'sms' || $alert->alert_method === 'both') {
            // Implement SMS notification service
            // This would typically use an SMS gateway service
            // For now, we'll just log the message
            \Log::info('SMS Alert: Low inventory for product ' . $product->name);
        }

        $alert->update(['last_checked_at' => now()]);
    }
}
