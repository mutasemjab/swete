<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends ModuleController
{
    protected string $module = 'tenders';

    public function index(Request $request)
    {
        $query = Project::with(['customer', 'tender']);

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
        $project->load(['customer', 'tender', 'creator', 'purchaseRequests.supplier']);

        return $this->moduleView('tenders.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return $this->moduleView('tenders.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'status'   => ['required', 'in:active,completed,cancelled'],
            'notes'    => ['nullable', 'string'],
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
}
