<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceNotification;
use App\Models\User;
use App\Support\MaintenanceNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaintenanceNotificationController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        abort_unless(\App\Support\MaintenanceFeatures::ready(), 503, __('maintenance.field_workflow_pending'));
        return $this->moduleView('settings.maintenance-notifications.index', [
            'users' => User::where('status', true)->orderBy('name')->get(),
            'recipients' => DB::table('maintenance_notification_recipients')->get()->groupBy('kind'),
            'kinds' => MaintenanceNotifications::KINDS,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'visit_reminder' => ['nullable', 'array'],
            'visit_reminder.*' => ['integer', 'distinct', Rule::exists('users', 'id')->where('status', true)],
            'emergency_limit' => ['nullable', 'array'],
            'emergency_limit.*' => ['integer', 'distinct', Rule::exists('users', 'id')->where('status', true)],
        ]);
        DB::transaction(function () use ($data) {
            foreach (MaintenanceNotifications::KINDS as $kind) {
                DB::table('maintenance_notification_recipients')->where('kind', $kind)->delete();
                foreach ($data[$kind] ?? [] as $userId) {
                    DB::table('maintenance_notification_recipients')->insert(['kind' => $kind, 'user_id' => $userId]);
                }
            }
        });

        return back()->with('success', __('maintenance.notification_settings_saved'));
    }

    public function inbox(Request $request)
    {
        abort_unless(\App\Support\MaintenanceFeatures::ready(), 503, __('maintenance.field_workflow_pending'));
        $this->module = 'maintenance';

        return $this->moduleView('maintenance.notifications', [
            'notifications' => MaintenanceNotification::where('user_id', $request->user()->id)->latest()->paginate(25),
        ]);
    }

    public function read(Request $request, MaintenanceNotification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
        $notification->update(['read_at' => now()]);

        return back();
    }
}
