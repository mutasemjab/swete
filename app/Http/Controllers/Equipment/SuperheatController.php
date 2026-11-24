<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\EquipmentUnit;
use App\Support\Superheat;
use Illuminate\Http\Request;

class SuperheatController extends ModuleController
{
    protected string $module = 'equipment';

    public function index(Request $request)
    {
        $refrigerants = Superheat::availableRefrigerants();
        $unit = $request->filled('equipment_unit_id') ? EquipmentUnit::find($request->input('equipment_unit_id')) : null;
        $band = Superheat::bandForUnit($unit);

        $result = null;

        if ($request->filled(['refrigerant', 'suction_temp_c', 'suction_pressure', 'pressure_unit'])) {
            $validated = $request->validate([
                'refrigerant'       => ['required', 'string'],
                'suction_temp_c'    => ['required', 'numeric'],
                'suction_pressure'  => ['required', 'numeric'],
                'pressure_unit'     => ['required', 'in:bar,psi,kpa,mpa'],
            ]);

            $result = Superheat::calculate(
                $validated['refrigerant'],
                (float) $validated['suction_temp_c'],
                (float) $validated['suction_pressure'],
                $validated['pressure_unit'],
                $band,
            );
        }

        return $this->moduleView('equipment.superheat.index', [
            'refrigerants' => $refrigerants,
            'unit'         => $unit,
            'band'         => $band,
            'result'       => $result,
        ]);
    }
}
