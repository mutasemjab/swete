<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectItemAssignment;
use Illuminate\Http\Request;

class ProjectItemAssignmentController extends Controller
{
    /** Assign (or reassign) one of the project's current price-quote line items to an employee. */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'price_quote_item_id' => ['required', 'integer'],
            'assigned_to'          => ['required', 'exists:users,id'],
        ]);

        $itemBelongsToProject = $project->quoteItems()->contains('id', $validated['price_quote_item_id']);
        abort_unless($itemBelongsToProject, 404);

        ProjectItemAssignment::updateOrCreate(
            ['project_id' => $project->id, 'price_quote_item_id' => $validated['price_quote_item_id']],
            [
                'assigned_to' => $validated['assigned_to'],
                'is_done'     => false,
                'done_at'     => null,
                'created_by'  => $request->user()->id,
            ]
        );

        return back()->with('success', __('tenders.item_assigned_success'));
    }

    /** The assignee marks their own item done; anyone with project-edit rights may also mark it on their behalf. */
    public function markDone(Request $request, Project $project, ProjectItemAssignment $assignment)
    {
        abort_unless($assignment->project_id === $project->id, 404);
        abort_unless(
            $request->user()->id === $assignment->assigned_to || $request->user()->can('projects.edit') || $request->user()->can('tenders.projects.edit'),
            403
        );

        $assignment->update(['is_done' => true, 'done_at' => now()]);

        return back()->with('success', __('tenders.item_marked_done_success'));
    }
}
