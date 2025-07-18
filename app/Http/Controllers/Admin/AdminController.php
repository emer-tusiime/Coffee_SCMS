<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $pendingUsers = User::where('approved', false)
            ->where('role', '!=', 'admin')
            ->get();

        return view('admin.dashboard', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot approve admin accounts');
        }

        $user->update([
            'approved' => true,
            'status' => 'active',
            'approval_message' => 'Account approved by administrator'
        ]);

        $user->notify(new AccountApprovedNotification());

        return redirect()->back()->with('success', 'Account approved successfully');
    }

    public function reject(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot reject admin accounts');
        }

        $user->update([
            'approved' => false,
            'status' => 'rejected',
            'approval_message' => 'Account rejected by administrator'
        ]);

        return redirect()->back()->with('success', 'Account rejected successfully');
    }
}
