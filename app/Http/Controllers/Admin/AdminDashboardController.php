<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Production;
use App\Models\Inventory;
use App\Models\QualityIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            // Get statistics
            $totalOrders = Order::count() ?? 0;
            $productionEfficiency = Production::count() > 0 
                ? (Production::where('status', 'completed')->count() / Production::count() * 100)
                : 0;
            $inventoryAlerts = Inventory::count() > 0 
                ? Inventory::where('quantity', '<', DB::raw('min_quantity'))->count()
                : 0;
            $qualityIssues = QualityIssue::count() ?? 0;

            // Get pending accounts
            $pendingAccounts = User::pending()
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('admin.dashboard', [
                'totalOrders' => $totalOrders,
                'productionEfficiency' => $productionEfficiency,
                'inventoryAlerts' => $inventoryAlerts,
                'qualityIssues' => $qualityIssues,
                'pendingAccounts' => $pendingAccounts
            ]);
        } catch (\Exception $e) {
            // If tables don't exist yet, return default values
            return view('admin.dashboard', [
                'totalOrders' => 0,
                'productionEfficiency' => 0,
                'inventoryAlerts' => 0,
                'qualityIssues' => 0,
                'pendingAccounts' => collect([])
            ]);
        }
    }
}
