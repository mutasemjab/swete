<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            // Maintenance technicians get their own standalone mobile portal as their entire
            // experience — never the normal dashboard/sidebar. See DashboardController::index()
            // for the matching safety-net redirect if they ever navigate back to "/" directly.
            if ($user->hasRole('maintenance_technician')) {
                return redirect()->intended(route('maintenance-visits.mobile.create'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => __('app.login_error'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}
