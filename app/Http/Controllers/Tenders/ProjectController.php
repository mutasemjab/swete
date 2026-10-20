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
        $project->load(['customer', 'tender', 'creator', 'manager', 'engineer', 'foreman', 'purchaseRequests.supplier', 'attachments.creator', 'agreements']);
        $employees = User::where('status', true)->orderBy('name')->get();
        $quoteItems = $project->quoteItems();
        $financialSummary = $project->financialSummary();

        return $this->moduleView('tenders.projects.show', compact('project', 'employees', 'quoteItems', 'financialSummary'));
    }

    public function edit(Project $project)
    {
        $employees = User::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.projects.edit', compact('project', 'employees'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'title_en'   => ['nullable', 'string', 'max:255'],
            'status'     => ['required', 'in:under_review,active,completed,cancelled'],
            'notes'      => ['nullable', 'string'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', __('tenders.project_updated'));
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
