<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDevice;
use Illuminate\Http\Request;

class ProjectDeviceController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);

        $validated = $request->validate([
            'price_quote_item_id'  => ['required', 'integer'],
            'supply_date'           => ['nullable', 'date'],
            'installation_date'     => ['nullable', 'date'],
            'warranty_start_date'   => ['nullable', 'date'],
        ]);

        $itemBelongsToProject = $project->quoteItems()->contains('id', $validated['price_quote_item_id']);
        abort_unless($itemBelongsToProject, 404);

        ProjectDevice::updateOrCreate(
            ['project_id' => $project->id, 'price_quote_item_id' => $validated['price_quote_item_id']],
            [
                'supply_date'         => $validated['supply_date'] ?? null,
                'installation_date'   => $validated['installation_date'] ?? null,
                'warranty_start_date' => $validated['warranty_start_date'] ?? null,
                'created_by'          => $request->user()->id,
            ]
        );

        return back()->with('success', __('tenders.device_added'));
    }

    public function destroy(Project $project, ProjectDevice $device)
    {
        abort_unless($project->is_current, 404);
        abort_unless($device->project_id === $project->id, 404);

        $device->delete();

        return back()->with('success', __('tenders.device_deleted'));
    }
}
