<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Notifications\AccountPendingApprovalNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        event(new Registered($user));

        return redirect()->route('registration.success')
            ->with('success', 'Registration successful! Your account is pending admin approval.');
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $roles = User::getRoles();
        return view('auth.register', compact('roles'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:supplier,factory,wholesaler,retailer'],
            'contact_info' => ['required', 'string', 'max:255']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'contact_info' => $data['contact_info'],
            'status' => 'pending',
            'approved' => false,
            'approval_message' => 'Account pending approval'
        ]);

        // Temporarily disable email notifications during development
        // $user->notify(new AccountPendingApprovalNotification($user));

        // If the user is a factory, create a Factory record
        if ($user->role === 'factory') {
            \App\Models\Factory::create([
                'user_id' => $user->id,
                'name' => $data['name'] . "'s Factory",
                'location' => $data['contact_info'] ?? 'Unknown',
                'capacity' => 1000,
                'processing_type' => 'Washed',
                'quality_standard' => 'A',
                'status' => true,
                'contact_person' => $data['name'],
                'contact_email' => $data['email'],
                'contact_phone' => '0000000000',
                'operating_hours' => json_encode(['mon-fri' => '8-5']),
                'certifications' => json_encode(['ISO9001']),
            ]);
        }

        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $message = "Thank you for registering with Coffee SCMS! Your account is pending admin approval. You will receive an email notification once your account is approved. Thank you for your patience.";
        return redirect()->route('login')
            ->with('info', $message);
    }

    /**
     * Get the post registration redirect path.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return RouteServiceProvider::HOME;
    }
}
