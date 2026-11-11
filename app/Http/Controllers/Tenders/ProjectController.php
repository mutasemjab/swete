<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Project;
use App\Models\User;
use App\Support\BackNavigation;
use Illuminate\Http\Request;

class ProjectController extends ModuleController
{
    protected string $module = 'projects';

    public function index(Request $request)
    {
        // Newly-converted tenders land in their own "Projects Under Review" module/queue first
        // (see Tenders\ProjectReviewController) — they don't clutter the real projects list
        // until approved.
        $query = Project::with(['customer', 'tender'])->where('status', '!=', 'under_review')->where('is_current', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $projects = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('tenders.projects.index', compact('projects'));
    }

    public function show(Request $request, Project $project)
    {
        // Keeps the sidebar/title context matching wherever the project actually is right now,
        // not always "Projects" — mirrors Clients\ClientExtraFieldController's per-action module switch.
        $this->module = $project->status === 'under_review' ? 'projects_under_review' : 'projects';

        $project->load([
            'customer', 'tender', 'creator', 'manager', 'engineer', 'foreman',
            'purchaseRequests.supplier', 'attachments.creator', 'agreements', 'questions.askedTo', 'questions.askedBy',
            'versions', 'devices.item.material', 'devices.creator', 'analyses',
        ]);
        $employees = User::where('status', true)->orderBy('name')->get();
        $quoteItems = $project->quoteItems();

        // Anyone without project-edit rights only ever sees the line item(s) actually assigned to
        // THEM — they're not the one doing any assigning. Super-admin bypasses every permission
        // check already (see AuthServiceProvider), so it and anyone holding projects.edit/
        // tenders.projects.edit (who assign items to others) still see the full list.
        if (! auth()->user()->can('projects.edit') && ! auth()->user()->can('tenders.projects.edit')) {
            $quoteItems = $quoteItems->filter(function ($item) use ($project) {
                $assignment = $item->assignments->firstWhere('project_id', $project->id);

                return $assignment && $assignment->assigned_to === auth()->id();
            })->values();
        }

        $financialSummary = $project->financialSummary();
        // Preserve the existing status-aware default (an under-review project's "natural home" is
        // the review queue, not the main Projects index) as the explicit fallback.
        $backUrl = BackNavigation::url([], $project->status === 'under_review' ? 'project-reviews.index' : 'projects.index');

        return $this->moduleView('tenders.projects.show', compact('project', 'employees', 'quoteItems', 'financialSummary', 'backUrl'));
    }

    public function edit(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);
        $this->module = $project->status === 'under_review' ? 'projects_under_review' : 'projects';

        $backUrl = BackNavigation::url([$project], 'projects.show');

        return $this->moduleView('tenders.projects.edit', compact('project', 'backUrl'));
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);

        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'title_en'   => ['nullable', 'string', 'max:255'],
            'status'     => ['required', 'in:under_review,active,completed,cancelled'],
            'notes'      => ['nullable', 'string'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', [$project, 'back' => $request->input('back')])
            ->with('success', __('tenders.project_updated'));
    }

    /** The project manager is picked by whoever has edit rights — from the project's own show page, not edit. */
    public function assignManager(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);

        $validated = $request->validate([
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        $project->update($validated);

        return back()->with('success', __('tenders.project_team_updated'));
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', __('tenders.project_deleted'));
    }

    /** Only the project's own manager may set its engineer/foreman — enforced here, not via a permission string. */
    public function assignTeam(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);
        abort_unless($request->user()->id === $project->manager_id, 403, __('tenders.project_team_unauthorized'));

        $validated = $request->validate([
            'engineer_id' => ['nullable', 'exists:users,id'],
            'foreman_id'  => ['nullable', 'exists:users,id'],
        ]);

        $project->update($validated);

        return back()->with('success', __('tenders.project_team_updated'));
    }
}
