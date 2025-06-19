<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventAutoLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('login') && !Auth::check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
