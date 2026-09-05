<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends ModuleController
{
    protected string $module = 'settings';

    public function index(Request $request)
    {
        $query = User::with('roles')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->boolean('status'));
        }

        $users       = $query->paginate(15)->withQueryString();
        $roles       = Role::orderBy('name')->get();
        $totalUsers  = User::count();
        $activeUsers = User::where('status', true)->count();
        $admins      = User::whereHas('roles', fn ($q) => $q->where('name', 'super-admin'))->count();

        return $this->moduleView('settings.users.index', compact(
            'users', 'roles', 'totalUsers', 'activeUsers', 'admins'
        ));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return $this->moduleView('settings.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status'   => ['boolean'],
            'role'     => ['nullable', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status'   => $request->boolean('status', true),
        ]);

        if (! empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('settings.users.index')
            ->with('success', __('settings.user_added'));
    }

    public function show(User $user)
    {
        $user->load('roles.permissions');
        return $this->moduleView('settings.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return $this->moduleView('settings.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status'   => ['boolean'],
            'role'     => ['nullable', 'exists:roles,name'],
        ]);

        $user->update([
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'phone'  => $validated['phone'] ?? null,
            'status' => $request->boolean('status'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['role'] ? [$validated['role']] : []);

        return redirect()->route('settings.users.index')
            ->with('success', __('settings.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('settings.cannot_delete_self'));
        }

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', __('settings.user_deleted'));
    }
}
