<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceDevicePassword;
use Illuminate\Http\Request;

/** Simple reference storage for maintenance-related device credentials — not a security vault. */
class DevicePasswordController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index()
    {
        $devicePasswords = MaintenanceDevicePassword::orderBy('device_name')->get();
        return $this->moduleView('maintenance.device-passwords.index', compact('devicePasswords'));
    }

    public function create()
    {
        return $this->moduleView('maintenance.device-passwords.create', ['devicePassword' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        MaintenanceDevicePassword::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('maintenance-device-passwords.index')
            ->with('success', __('maintenance.device_password_added'));
    }

    public function edit(MaintenanceDevicePassword $maintenanceDevicePassword)
    {
        return $this->moduleView('maintenance.device-passwords.edit', ['devicePassword' => $maintenanceDevicePassword]);
    }

    public function update(Request $request, MaintenanceDevicePassword $maintenanceDevicePassword)
    {
        $maintenanceDevicePassword->update($this->validated($request));

        return redirect()->route('maintenance-device-passwords.index')
            ->with('success', __('maintenance.device_password_updated'));
    }

    public function destroy(MaintenanceDevicePassword $maintenanceDevicePassword)
    {
        $maintenanceDevicePassword->delete();

        return redirect()->route('maintenance-device-passwords.index')
            ->with('success', __('maintenance.device_password_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'device_name' => ['required', 'string', 'max:150'],
            'password'    => ['required', 'string', 'max:255'],
            'notes'       => ['nullable', 'string'],
        ]);
    }
}
