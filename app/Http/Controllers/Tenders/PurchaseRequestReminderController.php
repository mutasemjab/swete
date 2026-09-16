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
        return $this->moduleView('tenders.purchase-request-reminders.create', [
            ...$this->formOptions(),
            'reminder' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $reminder = PurchaseRequestReminder::create([
            'project_id'       => $validated['project_id'],
            'google_drive_url' => $validated['google_drive_url'],
            'status'           => 'pending',
            'requested_by'     => $request->user()->id,
        ]);

        $this->syncItems($reminder, $validated['items']);

        return redirect()->route('purchase-request-reminders.show', $reminder)
            ->with('success', __('tenders.reminder_added'));
    }

    public function show(PurchaseRequestReminder $purchaseRequestReminder)
    {
        $purchaseRequestReminder->load(['project.customer', 'requester', 'fulfiller', 'purchaseRequest', 'items.material.unit', 'items.features']);

        return $this->moduleView('tenders.purchase-request-reminders.show', ['reminder' => $purchaseRequestReminder]);
    }

    public function edit(PurchaseRequestReminder $purchaseRequestReminder)
    {
        abort_unless($purchaseRequestReminder->isEditable(), 403, __('tenders.reminder_locked'));

        $purchaseRequestReminder->load('items.features');

        return $this->moduleView('tenders.purchase-request-reminders.edit', [
            ...$this->formOptions(),
            'reminder' => $purchaseRequestReminder,
        ]);
    }

    public function update(Request $request, PurchaseRequestReminder $purchaseRequestReminder)
    {
        abort_unless($purchaseRequestReminder->isEditable(), 403, __('tenders.reminder_locked'));

        $validated = $this->validated($request);

        $purchaseRequestReminder->update([
            'project_id'       => $validated['project_id'],
            'google_drive_url' => $validated['google_drive_url'],
        ]);

        $this->syncItems($purchaseRequestReminder, $validated['items']);

        return redirect()->route('purchase-request-reminders.show', $purchaseRequestReminder)
            ->with('success', __('tenders.reminder_updated'));
    }

    public function destroy(PurchaseRequestReminder $purchaseRequestReminder)
    {
        abort_unless($purchaseRequestReminder->isEditable(), 403, __('tenders.reminder_locked'));

        $purchaseRequestReminder->delete();

        return redirect()->route('purchase-request-reminders.index')
            ->with('success', __('tenders.reminder_deleted'));
    }

    private function formOptions(): array
    {
        return [
            'projects'  => Project::where('status', 'active')->orderByDesc('created_at')->get(),
            'materials' => Material::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        // A pasted link with no scheme would otherwise render as a relative href and silently fail to open.
        $request->merge(['google_drive_url' => normalizeUrl($request->input('google_drive_url'))]);

        return $request->validate([
            'project_id'              => ['required', 'exists:projects,id'],
            'google_drive_url'        => ['required', 'url', 'max:500'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.material_id'     => ['required', 'exists:materials,id'],
            'items.*.quantity'        => ['required', 'numeric', 'min:0.001'],
            'items.*.ercd'            => ['nullable', 'string', 'max:150'],
            'items.*.unit_price'      => ['nullable', 'numeric', 'min:0'],
            'items.*.features'        => ['array'],
            'items.*.features.*'      => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncItems(PurchaseRequestReminder $reminder, array $items): void
    {
        $reminder->items()->delete();

        foreach ($items as $item) {
            $features = $item['features'] ?? [];
            unset($item['features']);

            $item['total'] = filled($item['unit_price'] ?? null) ? $item['quantity'] * $item['unit_price'] : null;

            $createdItem = $reminder->items()->create($item);

            foreach (array_filter($features, fn ($value) => filled($value)) as $value) {
                $createdItem->features()->create(['value' => $value]);
            }
        }
    }
}
