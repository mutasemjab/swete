<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Mirrors AuthController, against the 'customer' guard — logs in with phone+password, not email,
 * since phone is what a customer actually remembers (requested over the generated customer code).
 * Known limitation, accepted by the user: Customer.phone has no uniqueness constraint, so if two
 * customers share the same phone, Eloquent's credential lookup only ever matches the first one
 * found — the other can never log in under that phone until it's made unique or changed.
 */
class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('customer-portal.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('customer-portal.dashboard'));
        }

        return back()->withErrors([
            'phone' => __('customer_portal.login_error'),
        ])->onlyInput('phone');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer-portal.login');
    }
}
