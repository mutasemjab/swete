<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceReportMaterialApprover;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceReportMaterialApproverController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $users              = User::where('status', true)->orderBy('name')->get();
        $currentApproverIds = MaintenanceReportMaterialApprover::pluck('user_id');

        return $this->moduleView('settings.maintenance-report-material-approvers.index', compact('users', 'currentApproverIds'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'approver_ids'   => ['array'],
            'approver_ids.*' => ['exists:users,id'],
        ]);

        MaintenanceReportMaterialApprover::query()->delete();

        foreach ($validated['approver_ids'] ?? [] as $userId) {
            MaintenanceReportMaterialApprover::create(['user_id' => $userId]);
        }

        return redirect()->route('settings.maintenance-report-material-approvers.index')
            ->with('success', __('settings.maintenance_report_material_approvers_saved'));
    }
}
