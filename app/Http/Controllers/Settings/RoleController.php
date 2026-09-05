<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();
        return $this->moduleView('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);
        return $this->moduleView('settings.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:roles,name', 'regex:/^[a-z0-9_-]+$/'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('settings.roles.index')
            ->with('success', __('settings.role_added'));
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return $this->moduleView('settings.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions    = Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return $this->moduleView('settings.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100', "unique:roles,name,{$role->id}", 'regex:/^[a-z0-9_-]+$/'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('settings.roles.index')
            ->with('success', __('settings.role_updated'));
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', __('settings.cannot_delete_role'));
        }

        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', __('settings.role_deleted'));
    }
}
