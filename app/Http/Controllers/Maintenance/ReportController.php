<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportField;
use App\Models\MaintenanceReportTemplate;
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
            'templates' => MaintenanceReportTemplate::where('status', true)->with('material')->with('fields')->orderBy('name')->get(),
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id' => ['required', 'exists:maintenance_report_templates,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'date'        => ['required', 'date'],
            'notes'       => ['nullable', 'string'],
            'answers'     => ['nullable', 'array'],
        ]);

        $template = MaintenanceReportTemplate::with('fields')->findOrFail($validated['template_id']);

        $report = MaintenanceReport::create([
            'number'        => MaintenanceReport::nextNumber(),
            'template_id'   => $template->id,
            'template_name' => $template->name,
            'material_id'   => $template->material_id,
            'customer_id'   => $validated['customer_id'],
            'date'          => $validated['date'],
            'notes'         => $validated['notes'] ?? null,
            'created_by'    => $request->user()->id,
        ]);

        $this->snapshotFields($report, $template->fields, $request->input('answers', []));

        return redirect()->route('maintenance-reports.show', $report)
            ->with('success', __('maintenance.report_added'));
    }

    public function show(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load(['customer', 'material', 'template', 'creator', 'fields']);

        return $this->moduleView('maintenance.reports.show', ['report' => $maintenanceReport]);
    }

    public function edit(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load('fields');

        return $this->moduleView('maintenance.reports.edit', [
            'report'    => $maintenanceReport,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MaintenanceReport $maintenanceReport)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'date'        => ['required', 'date'],
            'notes'       => ['nullable', 'string'],
            'answers'     => ['nullable', 'array'],
        ]);

        $maintenanceReport->update([
            'customer_id' => $validated['customer_id'],
            'date'        => $validated['date'],
            'notes'       => $validated['notes'] ?? null,
        ]);

        // The report's OWN fields (its permanent snapshot) — only the answers can change here, never
        // the question/type/options, which stay frozen from the moment the report was first filled.
        $answers = $request->input('answers', []);

        foreach ($maintenanceReport->fields as $field) {
            $field->update(['answer' => $this->normalizeAnswer($field->type, $field->options ?? [], $answers[$field->id] ?? null, "answers.{$field->id}")]);
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

    /** @param  \Illuminate\Support\Collection<int,MaintenanceReportTemplateField>  $templateFields */
    private function snapshotFields(MaintenanceReport $report, $templateFields, array $answers): void
    {
        foreach ($templateFields as $field) {
            $answer = $this->normalizeAnswer($field->type, $field->options ?? [], $answers[$field->id] ?? null, "answers.{$field->id}");

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
