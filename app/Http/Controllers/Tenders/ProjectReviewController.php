<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Project;
use Illuminate\Http\Request;

/**
 * "Projects Under Review" — its own module/queue for tenders that were just converted into a
 * project but haven't been approved into the real Projects list yet. Same underlying `Project`
 * model/table as Tenders\ProjectController; viewing one (`projects.show`) and editing it stay on
 * that controller — this one only owns the review queue itself and the approve action.
 */
class ProjectReviewController extends ModuleController
{
    protected string $module = 'projects_under_review';

    public function index(Request $request)
    {
        $query = Project::with(['customer', 'tender'])->where('status', 'under_review');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $projects = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('tenders.project-reviews.index', compact('projects'));
    }

    public function approve(Project $project)
    {
        abort_unless($project->status === 'under_review', 404);

        $project->update(['status' => 'active']);

        return redirect()->route('projects.show', $project)
            ->with('success', __('tenders.project_approved'));
    }

    public function reject(Project $project)
    {
        abort_unless($project->status === 'under_review', 404);

        $project->delete();

        return redirect()->route('project-reviews.index')
            ->with('success', __('tenders.project_rejected'));
    }
}
