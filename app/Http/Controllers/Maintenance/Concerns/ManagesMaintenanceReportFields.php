<?php

namespace App\Http\Controllers\Maintenance\Concerns;

use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportField;
use App\Models\MaintenanceReportTemplateField;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

/**
 * Per-field answer handling shared by the desktop one-shot report form (Maintenance\ReportController)
 * and the mobile incremental-autosave visit flow (Maintenance\MobileVisitController) — extracted so
 * both paths validate/store answers identically without duplicating the logic.
 */
trait ManagesMaintenanceReportFields
{
    /** @param  \Illuminate\Support\Collection<int,MaintenanceReportTemplateField>  $templateFields */
    protected function snapshotFields(Request $request, MaintenanceReport $report, $templateFields): void
    {
        $answers = $request->input('answers', []);

        foreach ($templateFields as $field) {
            $answer = $field->type === 'images'
                ? $this->storeImageAnswer($request, "answers.{$field->id}", null)
                : $this->normalizeAnswer($field->type, $field->options ?? [], $answers[$field->id] ?? null, "answers.{$field->id}");

            $this->snapshotField($field, $report, $answer);
        }
    }

    /** Creates one permanent per-report field row from its template field, optionally with an answer already known. */
    protected function snapshotField(MaintenanceReportTemplateField $field, MaintenanceReport $report, ?string $answer = null): MaintenanceReportField
    {
        return MaintenanceReportField::create([
            'report_id'   => $report->id,
            'question'    => $field->question,
            'question_en' => $field->question_en,
            'type'        => $field->type,
            'options'     => $field->options,
            'order'       => $field->order,
            'answer'      => $answer,
        ]);
    }

    /** @param  \Illuminate\Support\Collection  $fields */
    protected function validateImageAnswers(Request $request, $fields, bool $optional = false): void
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
    protected function storeImageAnswer(Request $request, string $key, ?string $existingAnswer): ?string
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

    /** Autosave: uploads exactly one new photo and appends it to an 'images' field's existing JSON path array. */
    protected function appendImageUpload(UploadedFile $file, ?string $existingAnswerJson): string
    {
        $paths    = $existingAnswerJson ? (json_decode($existingAnswerJson, true) ?: []) : [];
        $filename = uploadImage('assets/uploads/maintenance-reports', $file);
        $paths[]  = 'assets/uploads/maintenance-reports/' . $filename;

        return json_encode($paths);
    }

    /** Autosave: removes one already-uploaded photo path from an 'images' field's JSON answer. */
    protected function removeImageFromAnswer(?string $existingAnswerJson, string $path): ?string
    {
        $paths = $existingAnswerJson ? (json_decode($existingAnswerJson, true) ?: []) : [];
        $paths = array_values(array_filter($paths, fn ($p) => $p !== $path));

        return empty($paths) ? null : json_encode($paths);
    }

    /** Validates one answer against its field's own (possibly snapshotted) type/options, blank meaning "not answered". */
    protected function normalizeAnswer(string $type, array $options, mixed $raw, string $errorKey): ?string
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

    protected function syncMaterials(MaintenanceReport $report, array $materials): void
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
}
