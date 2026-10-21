<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends ModuleController
{
    protected string $module = 'projects';

    public function index(Request $request)
    {
        // Newly-converted tenders land in their own "Projects Under Review" module/queue first
        // (see Tenders\ProjectReviewController) — they don't clutter the real projects list
        // until approved.
        $query = Project::with(['customer', 'tender'])->where('status', '!=', 'under_review');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $projects = $query->latest()->paginate(20)->withQueryString();

        return $this->moduleView('tenders.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        // Keeps the sidebar/title context matching wherever the project actually is right now,
        // not always "Projects" — mirrors Clients\ClientExtraFieldController's per-action module switch.
        $this->module = $project->status === 'under_review' ? 'projects_under_review' : 'projects';

        $project->load([
            'customer', 'tender', 'creator', 'manager', 'engineer', 'foreman',
            'purchaseRequests.supplier', 'attachments.creator', 'agreements', 'questions.askedTo', 'questions.askedBy',
        ]);
        $employees = User::where('status', true)->orderBy('name')->get();
        $quoteItems = $project->quoteItems();
        $financialSummary = $project->financialSummary();

        return $this->moduleView('tenders.projects.show', compact('project', 'employees', 'quoteItems', 'financialSummary'));
    }

    public function edit(Project $project)
    {
        $this->module = $project->status === 'under_review' ? 'projects_under_review' : 'projects';

        return $this->moduleView('tenders.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'title_en'   => ['nullable', 'string', 'max:255'],
            'status'     => ['required', 'in:under_review,active,completed,cancelled'],
            'notes'      => ['nullable', 'string'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', __('tenders.project_updated'));
    }

    /** The project manager is picked by whoever has edit rights — from the project's own show page, not edit. */
    public function assignManager(Request $request, Project $project)
    {
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
        abort_unless($request->user()->id === $project->manager_id, 403, __('tenders.project_team_unauthorized'));

        $validated = $request->validate([
            'engineer_id' => ['nullable', 'exists:users,id'],
            'foreman_id'  => ['nullable', 'exists:users,id'],
        ]);

        $project->update($validated);

        return back()->with('success', __('tenders.project_team_updated'));
    }
}
