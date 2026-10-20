<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Project;
use App\Models\ProjectItemAssignment;

class DashboardController extends Controller
{
    public function index()
    {
        // Same rationale as the login redirect in AuthController — this role's entire
        // experience is the standalone mobile portal, never the normal dashboard.
        if (auth()->user()->hasRole('maintenance_technician')) {
            return redirect()->route('maintenance-visits.mobile.create');
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

        // Price-quote line items assigned to this employee, not yet checked off.
        $myAssignedItems = ProjectItemAssignment::with(['project', 'item.material'])
            ->where('assigned_to', auth()->id())
            ->where('is_done', false)
            ->latest()
            ->get();

        // Projects this employee manages, still open (under review or active).
        $myManagedProjects = Project::where('manager_id', auth()->id())
            ->whereIn('status', ['under_review', 'active'])
            ->latest()
            ->get();

        return view('dashboard.index', compact('modules', 'myAppointments', 'myAssignedItems', 'myManagedProjects'));
    }
}
