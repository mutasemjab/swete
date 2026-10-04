<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Material;
use App\Models\MaintenanceReportTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Manages the LIVE, editable question definitions used to fill out NEW reports.
 * Deliberately never touches an already-filled MaintenanceReport — those keep their own permanent
 * copy of every field (see MaintenanceReportField) regardless of what happens here afterward.
 */
class ReportTemplateController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index()
    {
        $templates = MaintenanceReportTemplate::with('material')->withCount('reports')->orderBy('name')->get();

        return $this->moduleView('maintenance.report-templates.index', compact('templates'));
    }

    public function create()
    {
        return $this->moduleView('maintenance.report-templates.create', [
            'template'  => null,
            'materials' => Material::confirmed()->where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $template = MaintenanceReportTemplate::create([
            'material_id' => $validated['material_id'],
            'name'        => $validated['name'],
            'name_en'     => $validated['name_en'] ?? null,
            'status'      => $request->boolean('status', true),
            'created_by'  => $request->user()->id,
        ]);

        $this->syncFields($template, $validated['fields']);

        return redirect()->route('report-templates.index')
            ->with('success', __('maintenance.template_added'));
    }

    public function edit(MaintenanceReportTemplate $reportTemplate)
    {
        $reportTemplate->load('fields');

        return $this->moduleView('maintenance.report-templates.edit', [
            'template'  => $reportTemplate,
            'materials' => Material::confirmed()->where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MaintenanceReportTemplate $reportTemplate)
    {
        $validated = $this->validated($request);

        $reportTemplate->update([
            'material_id' => $validated['material_id'],
            'name'        => $validated['name'],
            'name_en'     => $validated['name_en'] ?? null,
            'status'      => $request->boolean('status'),
        ]);

        $this->syncFields($reportTemplate, $validated['fields']);

        return redirect()->route('report-templates.index')
            ->with('success', __('maintenance.template_updated'));
    }

    /** Deletion is always safe: every filled report already carries its own permanent copy of its fields. */
    public function destroy(MaintenanceReportTemplate $reportTemplate)
    {
        $reportTemplate->delete();

        return redirect()->route('report-templates.index')
            ->with('success', __('maintenance.template_deleted'));
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'material_id'          => ['required', 'exists:materials,id'],
            'name'                 => ['required', 'string', 'max:150'],
            'name_en'              => ['nullable', 'string', 'max:150'],
            'status'               => ['boolean'],
            'fields'               => ['required', 'array', 'min:1'],
            'fields.*.question'    => ['required', 'string', 'max:255'],
            'fields.*.question_en' => ['nullable', 'string', 'max:255'],
            'fields.*.type'        => ['required', 'string', 'in:' . implode(',', \App\Models\MaintenanceReportTemplateField::TYPES)],
            'fields.*.options'     => ['nullable', 'array'],
            'fields.*.options.*'   => ['nullable', 'string', 'max:150'],
        ]);

        foreach ($validated['fields'] as $index => $field) {
            if ($field['type'] === 'choice') {
                $options = array_values(array_filter(array_map('trim', $field['options'] ?? [])));

                if (count($options) < 2) {
                    throw ValidationException::withMessages([
                        "fields.$index.options" => __('maintenance.template_choice_options_min'),
                    ]);
                }

                $validated['fields'][$index]['options'] = $options;
            } else {
                $validated['fields'][$index]['options'] = null;
            }
        }

        return $validated;
    }

    private function syncFields(MaintenanceReportTemplate $template, array $fields): void
    {
        $template->fields()->delete();

        foreach ($fields as $index => $field) {
            $template->fields()->create([
                'question'    => $field['question'],
                'question_en' => $field['question_en'] ?? null,
                'type'        => $field['type'],
                'options'     => $field['options'],
                'order'       => $index,
            ]);
        }
    }
}
