<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceContract;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceVisit;
use Illuminate\Http\Request;

class CustomerPortalController extends Controller
{
    public function dashboard()
    {
        $customer = auth('customer')->user();

        $contracts         = $customer->maintenanceContracts()->with('payments', 'scheduledVisits')->orderByDesc('signed_date')->get();
        $maintenanceRequests = $customer->maintenanceRequests()->latest()->get();
        $visitsAwaitingReview = $customer->maintenanceVisits()->where('status', 'submitted_to_customer')->latest()->get();
        $pastVisits         = $customer->maintenanceVisits()->whereIn('status', ['customer_signed', 'closed'])->latest()->get();

        return view('customer-portal.dashboard', compact('customer', 'contracts', 'maintenanceRequests', 'visitsAwaitingReview', 'pastVisits'));
    }

    public function showContract(MaintenanceContract $maintenanceContract)
    {
        abort_unless($maintenanceContract->customer_id === auth('customer')->id(), 404);

        $maintenanceContract->load(['payments', 'scheduledVisits']);

        return view('customer-portal.contracts.show', ['contract' => $maintenanceContract]);
    }

    public function createRequest()
    {
        return view('customer-portal.maintenance-requests.create');
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'description'    => ['required', 'string'],
            'preferred_date' => ['nullable', 'date'],
        ]);

        MaintenanceRequest::create([
            ...$validated,
            'customer_id' => auth('customer')->id(),
            'status'      => 'pending',
        ]);

        return redirect()->route('customer-portal.dashboard')
            ->with('success', __('customer_portal.request_submitted'));
    }

    public function showVisit(MaintenanceVisit $maintenanceVisit)
    {
        abort_unless($maintenanceVisit->customer_id === auth('customer')->id(), 404);
        abort_unless(in_array($maintenanceVisit->status, ['submitted_to_customer', 'customer_signed', 'closed'], true), 404);

        $maintenanceVisit->load(['reports.fields', 'reports.materials.material', 'technician']);

        return view('customer-portal.visits.show', ['visit' => $maintenanceVisit]);
    }

    public function signVisit(Request $request, MaintenanceVisit $maintenanceVisit)
    {
        abort_unless($maintenanceVisit->customer_id === auth('customer')->id(), 404);
        abort_unless($maintenanceVisit->isSubmitted(), 403);

        $validated = $request->validate([
            'signature' => ['required', 'image', 'max:2048'],
        ]);

        $filename = uploadImage('assets/uploads/maintenance-visit-signatures', $validated['signature']);

        $maintenanceVisit->sign('assets/uploads/maintenance-visit-signatures/' . $filename);

        return redirect()->route('customer-portal.visits.show', $maintenanceVisit)
            ->with('success', __('customer_portal.visit_signed'));
    }
}
