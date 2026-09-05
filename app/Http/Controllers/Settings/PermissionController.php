<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use Spatie\Permission\Models\Permission;

class PermissionController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $total = Permission::count();

        return $this->moduleView('settings.permissions.index', compact('permissions', 'total'));
    }
}
