<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Mirrors AuthController, against the 'customer' guard — logs in with code+password, not email. */
class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('customer-portal.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'code'     => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('customer-portal.dashboard'));
        }

        return back()->withErrors([
            'code' => __('customer_portal.login_error'),
        ])->onlyInput('code');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer-portal.login');
    }
}
