<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        // Dummy view for maintenance
        return view('factory.maintenance.index');
    }
} 