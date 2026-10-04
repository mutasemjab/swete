<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\Material;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportField;
use App\Models\MaintenanceReportTemplate;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $query = MaintenanceReport::with(['customer', 'material']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('template_id')) {
            $query->where('template_id', $request->input('template_id'));
        }

        $reports   = $query->latest()->paginate(20)->withQueryString();
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $templates = MaintenanceReportTemplate::orderBy('name')->get();

        return $this->moduleView('maintenance.reports.index', compact('reports', 'customers', 'templates'));
    }

    public function create()
    {
        return $this->moduleView('maintenance.reports.create', [
            ...$this->formOptions(),
        ]);
    }

    /** Same create form, rendered inside the minimal mobile layout for the maintenance technician's phone. */
    public function mobileCreate()
    {
        return view('maintenance.reports.mobile-create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id' => ['required', 'exists:maintenance_report_templates,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'date'        => ['required', 'date'],
            'problem'     => ['nullable', 'string'],
            'solution'    => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
            'materials'                 => ['nullable', 'array'],
            'materials.*.material_id'   => ['required', 'exists:materials,id'],
            'materials.*.quantity'      => ['required', 'numeric', 'min:0.001'],
        ]);

        $template = MaintenanceReportTemplate::with('fields')->findOrFail($validated['template_id']);
        $this->validateImageAnswers($request, $template->fields);

        $report = MaintenanceReport::create([
            'number'        => MaintenanceReport::nextNumber(),
            'template_id'   => $template->id,
            'template_name' => $template->name,
            'material_id'   => $template->material_id,
            'customer_id'   => $validated['customer_id'],
            'date'          => $validated['date'],
            'problem'       => $validated['problem'] ?? null,
            'solution'      => $validated['solution'] ?? null,
            'notes'         => $validated['notes'] ?? null,
            'materials_approval_status' => 'none',
            'created_by'    => $request->user()->id,
        ]);

        $this->snapshotFields($request, $report, $template->fields);
        $this->syncMaterials($report, $validated['materials'] ?? []);

        $isMobile = $request->routeIs('maintenance-reports.mobile.store');

        return $isMobile
            ? redirect()->route('maintenance-reports.mobile.create')->with('success', __('maintenance.report_added'))
            : redirect()->route('maintenance-reports.show', $report)->with('success', __('maintenance.report_added'));
    }

    public function show(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load(['customer', 'material', 'template', 'creator', 'fields', 'materials.material', 'materialApprovals.user', 'issueVoucher', 'priceQuote']);

        return $this->moduleView('maintenance.reports.show', ['report' => $maintenanceReport]);
    }

    public function edit(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load('fields', 'materials.material');

        return $this->moduleView('maintenance.reports.edit', [
            'report'    => $maintenanceReport,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'materials' => Material::confirmed()->where('status', true)->orderBy('name')->get(),
            'materialStock' => $this->materialStock(),
        ]);
    }

    public function update(Request $request, MaintenanceReport $maintenanceReport)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'date'        => ['required', 'date'],
            'problem'     => ['nullable', 'string'],
            'solution'    => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
            'materials'                 => ['nullable', 'array'],
            'materials.*.material_id'   => ['required', 'exists:materials,id'],
            'materials.*.quantity'      => ['required', 'numeric', 'min:0.001'],
        ]);

        $this->validateImageAnswers($request, $maintenanceReport->fields, true);

        $maintenanceReport->update([
            'customer_id' => $validated['customer_id'],
            'date'        => $validated['date'],
            'problem'     => $validated['problem'] ?? null,
            'solution'    => $validated['solution'] ?? null,
            'notes'       => $validated['notes'] ?? null,
        ]);

        // The report's OWN fields (its permanent snapshot) — only the answers can change here, never
        // the question/type/options, which stay frozen from the moment the report was first filled.
        foreach ($maintenanceReport->fields as $field) {
            $answer = $field->type === 'images'
                ? $this->storeImageAnswer($request, "answers.{$field->id}", $field->answer)
                : $this->normalizeAnswer($field->type, $field->options ?? [], $request->input("answers.{$field->id}"), "answers.{$field->id}");

            $field->update(['answer' => $answer]);
        }

        // Materials-used can only be freely edited before it's been sent for approval — once it has,
        // changing the lines here would silently invalidate a decision the approvers already made.
        if ($maintenanceReport->materials_approval_status === 'none') {
            $this->syncMaterials($maintenanceReport, $validated['materials'] ?? []);
        }

        return redirect()->route('maintenance-reports.show', $maintenanceReport)
            ->with('success', __('maintenance.report_updated'));
    }

    public function destroy(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->delete();

        return redirect()->route('maintenance-reports.index')
            ->with('success', __('maintenance.report_deleted'));
    }

    /** Approve/reject this report's materials-used list, as the current user, if they have a still-pending decision row. */
    public function approveMaterials(Request $request, MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->recordMaterialDecision($request->user(), 'approved');

        return back()->with('success', __('maintenance.material_decision_recorded'));
    }

    public function rejectMaterials(Request $request, MaintenanceReport $maintenanceReport)
    {
        $validated = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $maintenanceReport->recordMaterialDecision($request->user(), 'rejected', $validated['note'] ?? null);

        return back()->with('success', __('maintenance.material_decision_recorded'));
    }

    /** Keyword search across every report's "problem" field, to find how a similar issue was solved before. */
    public function search(Request $request)
    {
        $reports = collect();

        if ($request->filled('q')) {
            $reports = MaintenanceReport::with(['customer', 'material'])
                ->where('problem', 'like', '%' . $request->input('q') . '%')
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        return $this->moduleView('maintenance.reports.search', compact('reports'));
    }

    /** Hands this report's context (customer + materials used) to a brand-new Price Quote. */
    public function convertToQuote(MaintenanceReport $maintenanceReport)
    {
        return redirect()->route('price-quotes.create', ['report_id' => $maintenanceReport->id]);
    }

    private function formOptions(): array
    {
        return [
            'templates' => MaintenanceReportTemplate::where('status', true)->with('material')->with('fields')->orderBy('name')->get(),
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'materials' => Material::confirmed()->where('status', true)->orderBy('name')->get(),
            'materialStock' => $this->materialStock(),
        ];
    }

    private function materialStock()
    {
        return MaterialStock::selectRaw('material_id, SUM(quantity) as qty')
            ->groupBy('material_id')
            ->pluck('qty', 'material_id');
    }

    private function syncMaterials(MaintenanceReport $report, array $materials): void
    {
        $report->materials()->delete();

        foreach ($materials as $row) {
            $report->materials()->create($row);
        }

        $hasMaterials = $report->materials()->exists();

        if ($hasMaterials && $report->materials_approval_status === 'none') {
            $report->seedMaterialApprovals();
        } elseif (! $hasMaterials) {
            $report->update(['materials_approval_status' => 'none']);
        }
    }

    /** @param  \Illuminate\Support\Collection<int,MaintenanceReportTemplateField>  $templateFields */
    private function snapshotFields(Request $request, MaintenanceReport $report, $templateFields): void
    {
        $answers = $request->input('answers', []);

        foreach ($templateFields as $field) {
            $answer = $field->type === 'images'
                ? $this->storeImageAnswer($request, "answers.{$field->id}", null)
                : $this->normalizeAnswer($field->type, $field->options ?? [], $answers[$field->id] ?? null, "answers.{$field->id}");

            MaintenanceReportField::create([
                'report_id'   => $report->id,
                'question'    => $field->question,
                'question_en' => $field->question_en,
                'type'        => $field->type,
                'options'     => $field->options,
                'order'       => $field->order,
                'answer'      => $answer,
            ]);
        }
    }

    /** @param  \Illuminate\Support\Collection  $fields */
    private function validateImageAnswers(Request $request, $fields, bool $optional = false): void
    {
        foreach ($fields as $field) {
            if ($field->type === 'images') {
                $request->validate([
                    "answers.{$field->id}"   => [$optional ? 'sometimes' : 'nullable', 'array'],
                    "answers.{$field->id}.*" => ['nullable', 'image', 'max:5120'],
                ]);
            }
        }
    }

    /** Uploads any newly-submitted photos for one 'images' field; keeps the existing answer untouched if none were submitted this time. */
    private function storeImageAnswer(Request $request, string $key, ?string $existingAnswer): ?string
    {
        $files = array_filter($request->file($key, []));

        if (empty($files)) {
            return $existingAnswer;
        }

        $paths = [];

        foreach ($files as $file) {
            $filename = uploadImage('assets/uploads/maintenance-reports', $file);
            $paths[]  = 'assets/uploads/maintenance-reports/' . $filename;
        }

        return json_encode($paths);
    }

    /** Validates one answer against its field's own (possibly snapshotted) type/options, blank meaning "not answered". */
    private function normalizeAnswer(string $type, array $options, mixed $raw, string $errorKey): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if ($type === 'number' && ! is_numeric($raw)) {
            throw ValidationException::withMessages([$errorKey => __('maintenance.answer_invalid_number')]);
        }

        if ($type === 'boolean' && ! in_array($raw, ['0', '1'], true)) {
            throw ValidationException::withMessages([$errorKey => __('maintenance.answer_invalid_boolean')]);
        }

        if ($type === 'choice' && ! in_array($raw, $options, true)) {
            throw ValidationException::withMessages([$errorKey => __('maintenance.answer_invalid_choice')]);
        }

        return (string) $raw;
    }
}
