<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectQuestion;
use Illuminate\Http\Request;

class ProjectQuestionController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->is_current, 404);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'asked_to' => ['nullable', 'exists:users,id'],
        ]);

        $project->questions()->create([
            ...$validated,
            'asked_by' => $request->user()->id,
        ]);

        return back()->with('success', __('tenders.question_added'));
    }

    /** The target employee answers their own question; anyone with project-edit rights may also answer on their behalf. */
    public function answer(Request $request, Project $project, ProjectQuestion $question)
    {
        abort_unless($project->is_current, 404);
        abort_unless($question->project_id === $project->id, 404);
        abort_unless(
            $request->user()->id === $question->asked_to || $request->user()->can('projects.edit') || $request->user()->can('tenders.projects.edit'),
            403
        );

        $validated = $request->validate([
            'answer' => ['required', 'string'],
        ]);

        $question->update([
            'answer'      => $validated['answer'],
            'answered_at' => now(),
        ]);

        return back()->with('success', __('tenders.question_answered'));
    }
}
