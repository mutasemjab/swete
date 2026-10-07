<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceRequestController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $query = MaintenanceRequest::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $maintenanceRequests = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('maintenance.requests.index', compact('maintenanceRequests'));
    }

    public function approve(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($request->user()->is_maintenance_manager, 403);

        $maintenanceRequest->approve($request->user());

        return back()->with('success', __('maintenance.request_approved'));
    }

    public function reject(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        abort_unless($request->user()->is_maintenance_manager, 403);

        $validated = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $maintenanceRequest->reject($request->user(), $validated['note'] ?? null);

        return back()->with('success', __('maintenance.request_rejected'));
    }
}
