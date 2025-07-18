<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user is not admin and account is not approved
            if ($user->role !== 'admin' && !$user->approved) {
                // If account is rejected, show rejection message
                if ($user->status === 'rejected') {
                    Auth::logout();
                    return Redirect::route('login')->withErrors([
                        'message' => "Your account has been rejected. Reason: {$user->approval_message}"
                    ]);
                }
                
                // If account is pending, show pending message
                if ($user->status === 'pending') {
                    Auth::logout();
                    return Redirect::route('login')->withErrors([
                        'message' => 'Your account is pending approval. Please wait for an admin to review your account.'
                    ]);
                }
            }
        }

        return $next($request);
    }
}
