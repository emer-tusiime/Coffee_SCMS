<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionLineController extends Controller
{
    public function index()
    {
        // Dummy view for production lines
        return view('factory.production.lines.index');
    }
} 