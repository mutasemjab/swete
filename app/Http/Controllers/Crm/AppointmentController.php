<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\ModuleController;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends ModuleController
{
    protected string $module = 'crm';

    public function index(Request $request)
    {
        $query = Appointment::with(['type', 'customer', 'assignee']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        foreach (['status', 'appointment_type_id', 'assigned_to'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->input('date_to'));
        }

        // Open appointments first (enum order: scheduled, completed), soonest date first within each group.
        $appointments = $query->orderBy('status')->orderBy('appointment_date')->paginate(20)->withQueryString();

        return $this->moduleView('crm.appointments.index', [
            'appointments' => $appointments,
            'types'        => AppointmentType::orderBy('name')->get(),
            'employees'    => User::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return $this->moduleView('crm.appointments.create', [
            ...$this->formOptions(),
            'appointment' => null,
        ]);
    }

    public function store(Request $request)
    {
        Appointment::create([
            ...$this->validated($request),
            'status'     => 'scheduled',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', __('crm.appointment_added'));
    }

    public function edit(Appointment $appointment)
    {
        return $this->moduleView('crm.appointments.edit', [
            ...$this->formOptions($appointment),
            'appointment' => $appointment,
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $appointment->update($this->validated($request));

        return redirect()->route('appointments.index')
            ->with('success', __('crm.appointment_updated'));
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', __('crm.appointment_deleted'));
    }

    /** Ticks an appointment off (stopping its reminder), or reopens it. */
    public function toggleComplete(Appointment $appointment)
    {
        if ($appointment->status === 'completed') {
            $appointment->reopen();
            $message = __('crm.appointment_reopened');
        } else {
            $appointment->markCompleted();
            $message = __('crm.appointment_completed');
        }

        return back()->with('success', $message);
    }

    private function formOptions(?Appointment $appointment = null): array
    {
        return [
            'types'     => AppointmentType::selectable($appointment?->appointment_type_id),
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'employees' => User::where('status', true)
                ->when($appointment, fn ($query) => $query->orWhere('id', $appointment->assigned_to))
                ->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'appointment_date'    => ['required', 'date'],
            'appointment_type_id' => ['required', 'exists:appointment_types,id'],
            'customer_id'         => ['nullable', 'exists:customers,id'],
            'assigned_to'         => ['required', 'exists:users,id'],
            'notes'               => ['nullable', 'string'],
        ]);
    }
}
