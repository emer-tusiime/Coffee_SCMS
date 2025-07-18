<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Models\CoffeeSale;
use Illuminate\Http\Request;

class CoffeeSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $pendingSales = CoffeeSale::with('supplier')
            ->where('approved', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $approvedSales = CoffeeSale::with('supplier')
            ->where('approved', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $totalSales = CoffeeSale::sum('quantity');
        $totalValue = CoffeeSale::sum('total_value');

        return view('admin.coffee-sales', [
            'pendingSales' => $pendingSales,
            'approvedSales' => $approvedSales,
            'totalSales' => $totalSales,
            'totalValue' => $totalValue
        ]);
    }

    public function approve(CoffeeSale $sale)
    {
        $sale->update(['approved' => true]);
        return redirect()->back()->with('success', 'Coffee sale approved successfully.');
    }

    public function reject(CoffeeSale $sale)
    {
        $sale->delete();
        return redirect()->back()->with('success', 'Coffee sale rejected and removed.');
    }

    public function analytics()
    {
        $monthlySales = CoffeeSale::select(
            \DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            \DB::raw('SUM(quantity) as total_quantity'),
            \DB::raw('SUM(total_value) as total_value')
        )
        ->where('approved', true)
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(12)
        ->get();

        $qualityGrades = CoffeeSale::select('quality_grade', \DB::raw('COUNT(*) as count'))
            ->where('approved', true)
            ->groupBy('quality_grade')
            ->get();

        return view('admin.coffee-sales.analytics', [
            'monthlySales' => $monthlySales,
            'qualityGrades' => $qualityGrades
        ]);
    }
}
