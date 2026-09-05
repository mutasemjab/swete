<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends ModuleController
{
    protected string $module = 'settings';

    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->input('causer_id'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->input('subject_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $activities = $query->paginate(30)->withQueryString();

        $subjectTypes = Activity::query()
            ->whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type');

        $causers = User::orderBy('name')->get(['id', 'name']);

        return $this->moduleView('settings.activity-log.index', compact('activities', 'subjectTypes', 'causers'));
    }
}
