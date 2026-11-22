<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'currentModule' => 'profile',
            'currentModuleConfig' => [
                'name' => 'app.profile',
                'icon' => 'user',
                'color' => 'indigo',
                'route' => 'profile.edit',
                'gradient' => 'from-indigo-500 to-indigo-700',
                'sections' => [],
            ],
        ]);
    }

    public function update(Request $request)
    {
        // Always use the signed-in account; never accept a user ID from the form.
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required_with' => __('profile.current_password_required'),
            'current_password.current_password' => __('profile.current_password_incorrect'),
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', __('profile.updated'));
    }
}
