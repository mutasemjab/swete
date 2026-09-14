<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Material;
use App\Models\Project;
use App\Models\PurchaseRequestReminder;
use Illuminate\Http\Request;

class PurchaseRequestReminderController extends ModuleController
{
    protected string $module = 'tenders';

    public function index()
    {
        $reminders = PurchaseRequestReminder::with(['project', 'requester', 'purchaseRequest'])
            ->latest()->paginate(20);

        return $this->moduleView('tenders.purchase-request-reminders.index', compact('reminders'));
    }

    public function create()
    {
        $projects  = Project::where('status', 'active')->orderByDesc('created_at')->get();
        $materials = Material::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.purchase-request-reminders.create', compact('projects', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'              => ['required', 'exists:projects,id'],
            'google_drive_url'        => ['required', 'string', 'max:500'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.material_id'     => ['required', 'exists:materials,id'],
            'items.*.quantity'        => ['required', 'numeric', 'min:0.001'],
        ]);

        $reminder = PurchaseRequestReminder::create([
            'project_id'       => $validated['project_id'],
            'google_drive_url' => $validated['google_drive_url'],
            'status'           => 'pending',
            'requested_by'     => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            $reminder->items()->create($item);
        }

        return redirect()->route('purchase-request-reminders.show', $reminder)
            ->with('success', __('tenders.reminder_added'));
    }

    public function show(PurchaseRequestReminder $purchaseRequestReminder)
    {
        $purchaseRequestReminder->load(['project.customer', 'requester', 'fulfiller', 'purchaseRequest', 'items.material.unit']);

        return $this->moduleView('tenders.purchase-request-reminders.show', ['reminder' => $purchaseRequestReminder]);
    }
}
