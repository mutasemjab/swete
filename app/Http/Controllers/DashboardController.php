<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        $modules = config('modules');

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
