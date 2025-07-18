<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\QualityComplaintNotification;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $admin = User::where('role', 'admin')->first();
        $product = Product::find($validated['product_id']);
        $factory = Auth::user();

        // Notify admin
        Notification::send($admin, new QualityComplaintNotification(
            $factory,
            $product,
            $validated['message']
        ));

        return redirect()->back()->with('success', 'Complaint submitted to admin.');
    }
} 