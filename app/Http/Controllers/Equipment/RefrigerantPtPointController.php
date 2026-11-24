<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\RefrigerantPtPoint;
use Illuminate\Http\Request;

/** The real P-T chart points an engineer enters per refrigerant — never pre-seeded, see the migration's own comment. */
class RefrigerantPtPointController extends ModuleController
{
    protected string $module = 'equipment';

    public function index(Request $request)
    {
        $query = RefrigerantPtPoint::with('creator');

        if ($request->filled('refrigerant')) {
            $query->where('refrigerant', $request->input('refrigerant'));
        }

        $points = $query->orderBy('refrigerant')->orderBy('temperature_c')->get();
        $refrigerants = RefrigerantPtPoint::select('refrigerant')->distinct()->orderBy('refrigerant')->pluck('refrigerant');

        return $this->moduleView('equipment.refrigerant-pt-points.index', compact('points', 'refrigerants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'refrigerant'   => ['required', 'string', 'max:30'],
            'temperature_c' => ['required', 'numeric', 'between:-100,100'],
            'pressure_bar'  => ['required', 'numeric', 'min:0.001', 'max:1000'],
            'source'        => ['nullable', 'string', 'max:255'],
        ]);

        RefrigerantPtPoint::updateOrCreate(
            ['refrigerant' => strtoupper($validated['refrigerant']), 'temperature_c' => $validated['temperature_c']],
            ['pressure_bar' => $validated['pressure_bar'], 'source' => $validated['source'] ?? null, 'created_by' => $request->user()->id],
        );

        return back()->with('success', __('equipment.pt_point_added'));
    }

    public function destroy(RefrigerantPtPoint $refrigerantPtPoint)
    {
        $refrigerantPtPoint->delete();

        return back()->with('success', __('equipment.pt_point_deleted'));
    }
}
