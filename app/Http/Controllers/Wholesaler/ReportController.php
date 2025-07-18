<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('wholesaler.reports.index');
    }
} 