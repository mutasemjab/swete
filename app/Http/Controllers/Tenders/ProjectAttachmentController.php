<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Support\FileStorage;
use Illuminate\Http\Request;

class ProjectAttachmentController extends ModuleController
{
    protected string $module = 'tenders';

    /** Accepts one or more {name, file} rows in a single submit — see projects/show.blade.php's dynamic-row form. */
    public function store(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);

        $validated = $request->validate([
            'attachments'          => ['nullable', 'array'],
            'attachments.*.name'   => ['nullable', 'string', 'max:150'],
            'attachments.*.file'   => ['nullable', 'file', 'max:10240'],
        ]);

        foreach ($validated['attachments'] ?? [] as $row) {
            if (empty($row['file'])) {
                continue;
            }

            $project->attachments()->create([
                'name'       => $row['name'] ?: $row['file']->getClientOriginalName(),
                'path'       => FileStorage::store($row['file'], 'projects'),
                'created_by' => $request->user()->id,
            ]);
        }

        return back()->with('success', __('tenders.project_attachment_added'));
    }

    public function destroy(Project $project, ProjectAttachment $attachment)
    {
        abort_unless($attachment->project_id === $project->id, 404);

        FileStorage::delete($attachment->path);

        $attachment->delete();

        return back()->with('success', __('tenders.project_attachment_deleted'));
    }
}
