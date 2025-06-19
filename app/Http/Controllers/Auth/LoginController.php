<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to their appropriate dashboards. The controller uses a trait
    | to conveniently provide its functionality to your application.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect($this->redirectTo());
        }
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if the user exists and is active
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && !$user->isActive()) {
            throw ValidationException::withMessages([
                $this->username() => ['Your account is not active. Please contact support.'],
            ]);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the post-authentication redirect path for the user.
     */
    protected function redirectTo()
    {
        if (!auth()->check()) {
            return route('login');
        }

        $user = auth()->user();
        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'factory':
                return route('factory.dashboard');
            case 'supplier':
                return route('supplier.dashboard');
            case 'customer':
                return route('customer.dashboard');
            case 'retailer':
                return route('retailer.dashboard');
            case 'wholesaler':
                return route('wholesaler.dashboard');
            case 'workforce_manager':
                return route('workforce_manager.dashboard');
            default:
                return route('login');
        }
    }

    /**
     * Handle successful authentication.
     */
    protected function authenticated(Request $request, $user)
    {
        Log::info('Authentication successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);
    }

    /**
     * Handle failed authentication.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        Log::warning('Failed login attempt', ['email' => $request->email]);
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Log::info('User logged out', ['user_id' => auth()->id()]);

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
