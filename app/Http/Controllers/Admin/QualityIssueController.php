<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QualityIssue;
use Illuminate\Http\Request;

class QualityIssueController extends Controller
{
    public function index()
    {
        $qualityIssues = QualityIssue::with(['product', 'reportedBy'])
            ->latest()
            ->get();

        return view('admin.quality.issues', [
            'qualityIssues' => $qualityIssues
        ]);
    }
}
