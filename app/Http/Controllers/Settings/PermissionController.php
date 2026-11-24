<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\User;
use App\Support\PermissionLabels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends ModuleController
{
    protected string $module = 'settings';

    public function index(Request $request)
    {
        $validated = $request->validate([
            'role' => ['nullable', 'integer', Rule::exists('roles', 'id')->where('guard_name', 'web')],
            'user' => ['nullable', 'integer', 'exists:users,id'],
        ]);
        $permissions = Permission::with('roles')->where('guard_name', 'web')->orderBy('name')
            ->get()->groupBy(fn ($permission) => explode('.', $permission->name)[0]);
        $total = $permissions->sum(fn ($group) => $group->count());
        $roles = Role::with('users', 'permissions')->where('guard_name', 'web')->orderBy('name')->get();
        $selectedUser = ! empty($validated['user']) ? User::findOrFail($validated['user']) : null;
        $selectedRole = ! empty($validated['role'])
            ? $roles->firstWhere('id', $validated['role'])
            : ($selectedUser ? $roles->first(fn ($role) => $role->users->contains('id', $selectedUser->id)) : null);
        $isSuperAdmin = $selectedRole?->name === 'super-admin';
        $selectedPermissions = $selectedRole
            ? ($isSuperAdmin ? $permissions->flatten()->pluck('name')->all() : $selectedRole->permissions->pluck('name')->all())
            : [];
        $canEdit = $selectedRole && ! $isSuperAdmin && $request->user()->can('settings.roles.edit');

        return $this->moduleView('settings.permissions.index', compact(
            'permissions', 'total', 'roles', 'selectedUser', 'selectedRole',
            'isSuperAdmin', 'selectedPermissions', 'canEdit'
        ));
    }

    public function updateRole(Request $request, Role $role)
    {
        abort_unless($request->user()->can('settings.permissions.view') && $request->user()->can('settings.roles.edit'), 403);
        abort_unless($role->guard_name === 'web', 404);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'string', 'distinct', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);

        DB::transaction(function () use ($role, $validated) {
            $role = Role::whereKey($role->id)->lockForUpdate()->firstOrFail();

            if ($role->name === 'super-admin') {
                throw ValidationException::withMessages(['permissions' => __('permissions_ui.super_admin_hint')]);
            }

            $role->syncPermissions($validated['permissions'] ?? []);
        });

        return redirect()->route('settings.permissions.index', ['role' => $role->id])
            ->with('success', __('permissions_ui.saved', ['name' => PermissionLabels::role($role->name)]));
    }
}
