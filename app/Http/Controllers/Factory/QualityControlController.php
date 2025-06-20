<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QualityControlController extends Controller
{
    public function index()
    {
        // Dummy view for quality control
        return view('factory.quality.index');
    }
} 