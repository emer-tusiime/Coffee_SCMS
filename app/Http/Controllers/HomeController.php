<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        if ($user) {
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'factory':
                    return redirect()->route('factory.dashboard');
                case 'supplier':
                    return redirect()->route('supplier.dashboard');
                case 'customer':
                    return redirect()->route('customer.dashboard');
                case 'retailer':
                    return redirect()->route('retailer.dashboard');
                case 'wholesaler':
                    return redirect()->route('wholesaler.dashboard');
                case 'workforce_manager':
                    return redirect()->route('workforce_manager.dashboard');
                default:
                    return redirect()->route('login');
            }
        }
        return redirect()->route('login');
    }
}
