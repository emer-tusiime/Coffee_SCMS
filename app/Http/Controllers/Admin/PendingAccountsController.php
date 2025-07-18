<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\AccountRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PendingAccountsController extends Controller
{
    /**
     * Show the pending accounts list
     */
    public function index(Request $request)
    {
        $query = User::where('approved', false)->where('status', 'pending');
        
        // Search functionality
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('contact_info', 'like', "%{$search}%");
            });
        }

        $pendingAccounts = $query->paginate(10);
        
        return view('admin.pending-accounts', compact('pendingAccounts'));
    }



    /**
     * Approve multiple accounts at once
     */
    public function bulkApprove(Request $request)
    {
        $this->authorize('approve', User::class);

        $accountIds = $request->input('selected_accounts', []);
        
        if (empty($accountIds)) {
            return redirect()->back()->with('error', 'No accounts selected for approval.');
        }
    }

    /**
     * Approve a single user account
     */
    public function approve(User $user)
    {
        $this->authorize('approve', $user);

        try {
            $user->update([
                'approved' => true,
                'status' => 'active',
                'approval_message' => 'Account approved by admin',
                'updated_at' => now()
            ]);

            $user->notify(new AccountApprovedNotification());

            Log::info('Account approved', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'status' => 'active'
            ]);

            return redirect()->back()->with('success', 'Account approved successfully. The user can now access their account.');
        } catch (\Exception $e) {
            Log::error('Failed to approve account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to approve account. Please try again.');
        }
    }

    /**
     * Reject a single user account
     */
    public function reject(Request $request, User $user)
    {
        $this->authorize('reject', $user);

        try {
            $rejectionReason = $request->input('rejection_reason', 'Account rejected by admin');
            
            $user->update([
                'approved' => false,
                'status' => 'rejected',
                'approval_message' => $rejectionReason,
                'updated_at' => now()
            ]);

            $user->notify(new AccountRejectedNotification($rejectionReason));

            Log::info('Account rejected', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'status' => 'rejected'
            ]);

            return redirect()->back()->with('success', 'Account rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reject account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to reject account. Please try again.');
        }
    }

        try {
            User::whereIn('id', $accountIds)->update([
                'approved' => true,
                'status' => 'active',
                'approval_message' => 'Account approved by admin'
            ]);

            // Notify all approved users
            $users = User::whereIn('id', $accountIds)->get();
            foreach ($users as $user) {
                $user->notify(new AccountApprovedNotification());
                Log::info('Account approved', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
            }

            return redirect()->back()->with('success', 'Selected accounts approved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to approve accounts', [
                'account_ids' => $accountIds,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to approve accounts. Please try again.');
        }
    }

    /**
     * Reject multiple accounts at once
     */
    public function bulkReject(Request $request)
    {
        $this->authorize('reject', User::class);

        $accountIds = $request->input('selected_accounts', []);
        $rejectionReason = $request->input('rejection_reason', 'Account rejected by admin');
        
        if (empty($accountIds)) {
            return redirect()->back()->with('error', 'No accounts selected for rejection.');
        }

        try {
            User::whereIn('id', $accountIds)->update([
                'approved' => false,
                'status' => 'rejected',
                'approval_message' => $rejectionReason
            ]);

            // Notify all rejected users
            $users = User::whereIn('id', $accountIds)->get();
            foreach ($users as $user) {
                $user->notify(new AccountRejectedNotification($rejectionReason));
                Log::info('Account rejected', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
            }

            return redirect()->back()->with('success', 'Selected accounts rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reject accounts', [
                'account_ids' => $accountIds,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to reject accounts. Please try again.');
        }
    }

    /**
     * Approve a pending account
     */
    public function approve(User $user)
    {
        $this->authorize('approve', $user);

        try {
            // Update user status to active and set approved to true
            $user->update([
                'approved' => true,
                'status' => 'active',
                'approval_message' => 'Account approved by admin',
                'updated_at' => now()
            ]);

            // Notify the user
            $user->notify(new AccountApprovedNotification());

            // Log the approval
            Log::info('Account approved', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'status' => 'active'
            ]);

            // Redirect with success message
            return redirect()->back()->with('success', 'Account approved successfully. The user can now access their account.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to approve account', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect with error message
            return redirect()->back()->with('error', 'Failed to approve account. Please try again.');
        }
    }

    /**
     * Reject a pending account with a reason
     */
    public function reject(User $user, Request $request)
    {
        $this->authorize('reject', $user);

        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        try {
            $user->update([
                'approved' => false,
                'status' => 'rejected',
                'approval_message' => $request->rejection_reason
            ]);

            // Notify the user
            $user->notify(new AccountRejectedNotification($request->rejection_reason));

            Log::info('Account rejected', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'reason' => $request->rejection_reason
            ]);

            return redirect()->back()->with('success', 'Account rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reject account', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to reject account. Please try again.');
        }
    }
}
