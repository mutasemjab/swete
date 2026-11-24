<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\AlarmCode;
use App\Models\ControlSystem;
use App\Models\Document;
use App\Support\AlarmSearch;
use Illuminate\Http\Request;

class AlarmCodeController extends ModuleController
{
    protected string $module = 'equipment';

    /** The Alarm Lookup page itself — a plain, deterministic search, never an AI assistant. */
    public function lookup(Request $request)
    {
        $results = collect();

        if ($request->filled('q')) {
            $results = AlarmSearch::search(
                $request->input('q'),
                $request->filled('control_system_id') ? (int) $request->input('control_system_id') : null,
            );
        }

        $controlSystems = ControlSystem::where('status', true)->orderBy('name')->get();

        return $this->moduleView('equipment.alarm-lookup.index', compact('results', 'controlSystems'));
    }

    public function index(Request $request)
    {
        $query = AlarmCode::with('controlSystem');

        if ($request->filled('control_system_id')) {
            $query->where('control_system_id', $request->input('control_system_id'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('code', 'like', "%{$search}%")->orWhere('name_en', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%"));
        }

        $alarms = $query->orderBy('control_system_id')->orderBy('code')->paginate(20)->withQueryString();
        $controlSystems = ControlSystem::where('status', true)->orderBy('name')->get();

        return $this->moduleView('equipment.alarm-codes.index', compact('alarms', 'controlSystems'));
    }

    public function create()
    {
        return $this->moduleView('equipment.alarm-codes.create', [
            'alarm' => null,
        ] + $this->pickers());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        AlarmCode::create([...$validated, 'created_by' => $request->user()->id]);

        return redirect()->route('alarm-codes.index')
            ->with('success', __('equipment.alarm_code_added'));
    }

    public function edit(AlarmCode $alarmCode)
    {
        return $this->moduleView('equipment.alarm-codes.edit', [
            'alarm' => $alarmCode,
        ] + $this->pickers());
    }

    public function update(Request $request, AlarmCode $alarmCode)
    {
        $validated = $this->validated($request);

        $alarmCode->update($validated);

        return redirect()->route('alarm-codes.index')
            ->with('success', __('equipment.alarm_code_updated'));
    }

    public function destroy(AlarmCode $alarmCode)
    {
        $alarmCode->delete();

        return redirect()->route('alarm-codes.index')
            ->with('success', __('equipment.alarm_code_deleted'));
    }

    private function pickers(): array
    {
        return [
            'controlSystems' => ControlSystem::where('status', true)->orderBy('name')->get(),
            'documents'      => Document::where('is_archived', false)->orderBy('name')->get(['id', 'name', 'name_en']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'control_system_id'   => ['required', 'exists:control_systems,id'],
            'code'                => ['required', 'string', 'max:200'],
            'section'             => ['nullable', 'string', 'max:120'],
            'name_en'             => ['required', 'string', 'max:300'],
            'name_ar'             => ['nullable', 'string', 'max:300'],
            'causes_en'           => ['nullable', 'string'],
            'causes_ar'           => ['nullable', 'string'],
            'checks_en'           => ['nullable', 'string'],
            'checks_ar'           => ['nullable', 'string'],
            'effect_en'           => ['nullable', 'string'],
            'effect_ar'           => ['nullable', 'string'],
            'action_en'           => ['nullable', 'string'],
            'action_ar'           => ['nullable', 'string'],
            'reset_en'            => ['nullable', 'string'],
            'reset_ar'            => ['nullable', 'string'],
            'source_document_id'  => ['required', 'exists:documents,id'],
            'source_page'         => ['required', 'integer', 'min:1'],
        ]);
    }
}
