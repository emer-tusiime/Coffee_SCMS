<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        return view('admin.settings');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
        ]);

        // Update configuration
        config(['app.name' => $validated['company_name']]);
        config(['app.timezone' => $validated['timezone']]);
        config(['app.date_format' => $validated['date_format']]);

        // Save to .env
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        $envContent = str_replace(
            'APP_NAME=' . config('app.name'),
            'APP_NAME=' . $validated['company_name'],
            $envContent
        );

        $envContent = str_replace(
            'APP_TIMEZONE=' . config('app.timezone'),
            'APP_TIMEZONE=' . $validated['timezone'],
            $envContent
        );

        file_put_contents($envFile, $envContent);

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        // Update configuration
        config(['mail.from.address' => $validated['mail_from_address']]);
        config(['mail.from.name' => $validated['mail_from_name']]);

        // Save to .env
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        $envContent = str_replace(
            'MAIL_FROM_ADDRESS=' . config('mail.from.address'),
            'MAIL_FROM_ADDRESS=' . $validated['mail_from_address'],
            $envContent
        );

        $envContent = str_replace(
            'MAIL_FROM_NAME=' . config('mail.from.name'),
            'MAIL_FROM_NAME=' . $validated['mail_from_name'],
            $envContent
        );

        file_put_contents($envFile, $envContent);

        return redirect()->back()->with('success', 'Email settings updated successfully.');
    }
}
