<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Maintenance\Concerns\ManagesMaintenanceReportFields;
use App\Models\Customer;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportTemplate;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceVisit;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * The maintenance technician's standalone mobile portal — replaces the old one-shot
 * maintenance-reports.mobile.* flow. Every field saves immediately via AJAX (see updateAnswer()/
 * updateReport()/syncMaterials()) so a dropped connection or closed tab never loses more than
 * whatever single field wasn't saved yet; show() always re-derives the page from the database.
 */
class MobileVisitController extends Controller
{
    use ManagesMaintenanceReportFields;

    public function create(Request $request)
    {
        $open = MaintenanceVisit::where('created_by', $request->user()->id)->where('status', 'open')->first();

        if ($open) {
            return redirect()->route('maintenance-visits.mobile.show', $open);
        }

        // Keyed by customer id so the page can show only that customer's approved, not-yet-visited
        // requests once one is picked — small enough to bake into the page like materialStock is.
        $availableRequests = MaintenanceRequest::where('status', 'approved')
            ->whereNull('linked_visit_id')
            ->get()
            ->groupBy('customer_id')
            ->map(fn ($group) => $group->map(fn ($r) => [
                'id' => $r->id, 'description' => $r->description, 'preferred_date' => $r->preferred_date?->format('Y-m-d'),
            ])->values());

        return view('maintenance.visits.mobile-create', [
            'customers'         => Customer::where('status', true)->orderBy('name')->get(),
            'availableRequests' => $availableRequests,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'             => ['required', 'exists:customers,id'],
            'check_in_at'             => ['required', 'date'],
            'maintenance_request_id'  => ['nullable', 'exists:maintenance_requests,id'],
        ]);

        $visit = MaintenanceVisit::create([
            'customer_id'  => $validated['customer_id'],
            'created_by'   => $request->user()->id,
            'check_in_at'  => $validated['check_in_at'],
            'status'       => 'open',
        ]);

        if (! empty($validated['maintenance_request_id'])) {
            $maintenanceRequest = MaintenanceRequest::where('id', $validated['maintenance_request_id'])
                ->approvedAvailableFor($validated['customer_id'])
                ->first();

            if ($maintenanceRequest) {
                $maintenanceRequest->update(['linked_visit_id' => $visit->id]);
                $visit->update(['maintenance_request_id' => $maintenanceRequest->id]);
            }
        }

        return redirect()->route('maintenance-visits.mobile.show', $visit);
    }

    public function show(Request $request, MaintenanceVisit $maintenanceVisit)
    {
        abort_unless($maintenanceVisit->created_by === $request->user()->id, 403);

        if (! $maintenanceVisit->isOpen()) {
            return redirect()->route('maintenance-visits.mobile.create')
                ->with('success', __('maintenance.visit_already_closed'));
        }

        $maintenanceVisit->load(['customer', 'reports.fields', 'reports.materials']);

        return view('maintenance.visits.mobile-show', [
            'visit'         => $maintenanceVisit,
            'templates'     => MaintenanceReportTemplate::where('status', true)->with('material')->with('fields')->orderBy('name')->get(),
            'materials'     => Material::confirmed()->where('status', true)->orderBy('name')->get(),
            'materialStock' => $this->materialStock(),
        ]);
    }

    public function storeReport(Request $request, MaintenanceVisit $maintenanceVisit)
    {
        abort_unless($maintenanceVisit->created_by === $request->user()->id && $maintenanceVisit->isOpen(), 403);

        $validated = $request->validate([
            'template_id' => ['required', 'exists:maintenance_report_templates,id'],
        ]);

        $template = MaintenanceReportTemplate::with('fields')->findOrFail($validated['template_id']);

        $report = MaintenanceReport::create([
            'number'                    => MaintenanceReport::nextNumber(),
            'template_id'               => $template->id,
            'template_name'             => $template->name,
            'material_id'               => $template->material_id,
            'customer_id'               => $maintenanceVisit->customer_id,
            'date'                      => now()->toDateString(),
            'materials_approval_status' => 'none',
            'created_by'                => $request->user()->id,
            'visit_id'                  => $maintenanceVisit->id,
        ]);

        foreach ($template->fields as $field) {
            $this->snapshotField($field, $report, null);
        }

        $report->load('fields');

        return response()->json([
            'report' => [
                'id'     => $report->id,
                'number' => $report->number,
                'name'   => $template->localized_name,
            ],
            'fields' => $report->fields->map(fn ($f) => [
                'id' => $f->id, 'question' => $f->localized_question, 'type' => $f->type, 'options' => $f->options ?? [], 'answer' => $f->answer,
            ]),
        ]);
    }

    /** Per-field autosave: a text/number/choice/boolean answer, one new image upload, or one image removal. */
    public function updateAnswer(Request $request, MaintenanceVisit $maintenanceVisit, MaintenanceReport $maintenanceReport)
    {
        abort_unless($maintenanceVisit->created_by === $request->user()->id && $maintenanceVisit->isOpen(), 403);
        abort_unless($maintenanceReport->visit_id === $maintenanceVisit->id, 404);

        $validated = $request->validate([
            'field_id'    => ['required', 'exists:maintenance_report_fields,id'],
            'answer'      => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:5120'],
            'remove_path' => ['nullable', 'string'],
        ]);

        $field = $maintenanceReport->fields()->where('id', $validated['field_id'])->firstOrFail();

        try {
            if ($field->type === 'images') {
                if ($request->hasFile('image')) {
                    $answer = $this->appendImageUpload($request->file('image'), $field->answer);
                } elseif (! empty($validated['remove_path'])) {
                    $answer = $this->removeImageFromAnswer($field->answer, $validated['remove_path']);
                } else {
                    $answer = $field->answer;
                }
            } else {
                $answer = $this->normalizeAnswer($field->type, $field->options ?? [], $validated['answer'] ?? null, 'answer');
            }
        } catch (ValidationException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        $field->update(['answer' => $answer]);

        return response()->json(['ok' => true, 'answer' => $answer]);
    }

    /** Autosave for problem/solution/notes free text. */
    public function updateReport(Request $request, MaintenanceVisit $maintenanceVisit, MaintenanceReport $maintenanceReport)
    {
        abort_unless($maintenanceVisit->created_by === $request->user()->id && $maintenanceVisit->isOpen(), 403);
        abort_unless($maintenanceReport->visit_id === $maintenanceVisit->id, 404);

        $validated = $request->validate([
            'problem'  => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'notes'    => ['nullable', 'string'],
        ]);

        $maintenanceReport->update($validated);

        return response()->json(['ok' => true]);
    }

    public function syncMaterialsForReport(Request $request, MaintenanceVisit $maintenanceVisit, MaintenanceReport $maintenanceReport)
    {
        abort_unless($maintenanceVisit->created_by === $request->user()->id && $maintenanceVisit->isOpen(), 403);
        abort_unless($maintenanceReport->visit_id === $maintenanceVisit->id, 404);

        $validated = $request->validate([
            'materials'                => ['nullable', 'array'],
            'materials.*.material_id'  => ['required', 'exists:materials,id'],
            'materials.*.quantity'     => ['required', 'numeric', 'min:0.001'],
        ]);

        $this->syncMaterials($maintenanceReport, $validated['materials'] ?? []);

        return response()->json(['ok' => true]);
    }

    public function finish(Request $request, MaintenanceVisit $maintenanceVisit)
    {
        abort_unless($maintenanceVisit->created_by === $request->user()->id && $maintenanceVisit->isOpen(), 403);

        if ($maintenanceVisit->reports()->doesntExist()) {
            return back()->with('error', __('maintenance.visit_needs_one_report'));
        }

        $maintenanceVisit->finish();

        return redirect()->route('maintenance-visits.mobile.create')
            ->with('success', __('maintenance.visit_submitted_to_customer'));
    }

    private function materialStock()
    {
        return MaterialStock::selectRaw('material_id, SUM(quantity) as qty')
            ->groupBy('material_id')
            ->pluck('qty', 'material_id');
    }
}
