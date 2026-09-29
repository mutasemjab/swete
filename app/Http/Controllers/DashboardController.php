<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        // Same rationale as the login redirect in AuthController — this role's entire
        // experience is the standalone mobile portal, never the normal dashboard.
        if (auth()->user()->hasRole('maintenance_technician')) {
            return redirect()->route('maintenance-reports.mobile.create');
        }

        $modules = collect(config('modules'))
            ->filter(fn ($mod) => ! isset($mod['permission']) || auth()->user()->can($mod['permission']))
            ->all();

        // The signed-in employee's open appointments: overdue, today, and the coming week.
        $myAppointments = Appointment::with(['type', 'customer'])
            ->assignedTo(auth()->id())
            ->open()
            ->whereDate('appointment_date', '<=', today()->addDays(7))
            ->orderBy('appointment_date')
            ->get();

        return view('dashboard.index', compact('modules', 'myAppointments'));
    }
}
