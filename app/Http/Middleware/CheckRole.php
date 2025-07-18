<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        // Allow admin to access the admin.users.view-dashboard route
        if (Auth::user()->isAdmin() && $request->routeIs('admin.users.view-dashboard')) {
            return $next($request);
        }

        if (!Auth::user()->hasRole($role)) {
            // Redirect to their appropriate dashboard if they're logged in but wrong role
            return redirect()->route(Auth::user()->getDashboardRoute())
                ->with('error', 'Unauthorized access. You do not have the required role.');
        }

        return $next($request);
    }
}
