<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\ModuleController;
use App\Models\AppointmentType;
use Illuminate\Http\Request;

/** Dynamic/manageable list of appointment types — "add anything in the future" without code changes. */
class AppointmentTypeController extends ModuleController
{
    protected string $module = 'crm';

    public function index()
    {
        $types = AppointmentType::orderBy('name')->get();
        return $this->moduleView('crm.appointment-types.index', compact('types'));
    }

    public function create()
    {
        return $this->moduleView('crm.appointment-types.create', ['type' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        AppointmentType::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('appointment-types.index')
            ->with('success', __('crm.appointment_type_added'));
    }

    public function edit(AppointmentType $appointmentType)
    {
        return $this->moduleView('crm.appointment-types.edit', ['type' => $appointmentType]);
    }

    public function update(Request $request, AppointmentType $appointmentType)
    {
        $validated = $this->validated($request);

        $appointmentType->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('appointment-types.index')
            ->with('success', __('crm.appointment_type_updated'));
    }

    public function destroy(AppointmentType $appointmentType)
    {
        if ($appointmentType->appointments()->exists()) {
            return back()->with('error', __('crm.appointment_type_in_use'));
        }

        $appointmentType->delete();

        return redirect()->route('appointment-types.index')
            ->with('success', __('crm.appointment_type_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
