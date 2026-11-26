<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\EquipmentUnit;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractScheduledVisit;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportTemplate;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceVisit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TechnicianSubmission
{
    private array $stored = [];

    public function submit(User $user, array $data): MaintenanceVisit
    {
        $this->stored = [];
        try {
            return DB::transaction(function () use ($user, $data) {
                // Serialize retries from this technician, including simultaneous tabs/devices.
                User::whereKey($user->id)->lockForUpdate()->firstOrFail();
                $hash = hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                $existing = MaintenanceVisit::where('client_uuid', $data['uuid'])->first();
                if ($existing) {
                    abort_unless($existing->created_by === $user->id, 403);
                    abort_unless(hash_equals($existing->submission_hash ?? '', $hash), 409, __('maintenance.sync_payload_changed'));

                    return $existing->load('reports');
                }

                $customer = Customer::findOrFail($data['customer_id']);
                $contract = empty($data['contract_id']) ? null : MaintenanceContract::whereKey($data['contract_id'])->lockForUpdate()->firstOrFail();
                if ($contract) {
                    abort_unless($contract->customer_id === $customer->id, 422, __('maintenance.sync_wrong_customer'));
                    // Validate the recorded visit date, so a legitimate offline visit can arrive after expiry.
                    $date = substr($data['check_in_at'], 0, 10);
                    abort_unless($contract->status === 'active' && $date >= $contract->signed_date->toDateString() && $date <= $contract->expiry_date->toDateString(), 422, __('maintenance.sync_contract_inactive'));
                }
                $scheduled = empty($data['scheduled_visit_id']) ? null : MaintenanceContractScheduledVisit::whereKey($data['scheduled_visit_id'])->lockForUpdate()->firstOrFail();
                if ($scheduled) {
                    abort_unless($contract && $scheduled->contract_id === $contract->id, 422);
                    abort_if($scheduled->actualVisit()->exists(), 409, __('maintenance.sync_visit_already_recorded'));
                    $technicianId = $scheduled->schedule?->technician_id;
                    abort_if($technicianId && $technicianId !== $user->id, 403);
                    abort_unless($scheduled->type === $data['kind'], 422);
                }

                if (! empty($data['existing_visit_id'])) {
                    $visit = MaintenanceVisit::whereKey($data['existing_visit_id'])->lockForUpdate()->firstOrFail();
                    abort_unless($visit->created_by === $user->id && $visit->isOpen(), 409, __('maintenance.sync_visit_changed'));
                    abort_unless($visit->customer_id === $customer->id, 422);
                    $existingIds = $visit->reports()->pluck('id')->sort()->values()->all();
                    $submittedIds = collect($data['reports'])->pluck('existing_report_id')->filter()->sort()->values()->all();
                    abort_unless($existingIds == $submittedIds, 409, __('maintenance.sync_visit_changed'));
                } else {
                    $visit = new MaintenanceVisit(['customer_id' => $customer->id, 'created_by' => $user->id]);
                }
                $visit->fill([
                    'client_uuid' => $data['uuid'], 'submission_hash' => $hash,
                    'contract_id' => $contract?->id, 'scheduled_visit_id' => $scheduled?->id,
                    'kind' => $data['kind'], 'check_in_at' => $data['check_in_at'], 'status' => 'open',
                ])->save();

                if (! empty($data['maintenance_request_id'])) {
                    $maintenanceRequest = MaintenanceRequest::whereKey($data['maintenance_request_id'])->lockForUpdate()->firstOrFail();
                    abort_unless($maintenanceRequest->customer_id === $customer->id && $maintenanceRequest->status === 'approved'
                        && (! $maintenanceRequest->linked_visit_id || $maintenanceRequest->linked_visit_id === $visit->id), 409);
                    $maintenanceRequest->update(['linked_visit_id' => $visit->id]);
                    $visit->update(['maintenance_request_id' => $maintenanceRequest->id]);
                }

                $technicianSignature = $this->signature($data['technician_signature']);
                $customerSignature = $this->signature($data['customer_signature']);
                foreach ($data['reports'] as $row) {
                    $unit = empty($row['equipment_unit_id']) ? null : EquipmentUnit::with('model')->findOrFail($row['equipment_unit_id']);
                    if ($unit) {
                        abort_unless($unit->customer_id === $customer->id, 422, __('maintenance.sync_wrong_customer'));
                        if ($contract) {
                            abort_unless($contract->activeUnits()->where('equipment_unit_id', $unit->id)->exists(), 422, __('maintenance.sync_unit_not_covered'));
                        }
                    }
                    if ($scheduled?->equipment_unit_id) {
                        abort_unless($unit?->id === $scheduled->equipment_unit_id, 422);
                    }
                    if (! empty($row['existing_report_id'])) {
                        $report = MaintenanceReport::whereKey($row['existing_report_id'])->where('visit_id', $visit->id)->lockForUpdate()->firstOrFail();
                        abort_unless($report->content_status === 'received' && ! $report->technician_snapshot
                            && $report->updated_at->toISOString() === ($row['base_updated_at'] ?? null), 409, __('maintenance.sync_report_changed'));
                    } else {
                        abort_if(MaintenanceReport::where('client_uuid', $row['uuid'])->exists(), 409);
                        $template = empty($row['template_id']) ? null : MaintenanceReportTemplate::findOrFail($row['template_id']);
                        $report = new MaintenanceReport([
                            'number' => 'MR-'.now()->format('Y').'-'.strtoupper(str_replace('-', '', substr($row['uuid'], 0, 13))),
                            'template_id' => $template?->id, 'template_name' => $template?->name ?? $row['title'],
                            'material_id' => $template?->material_id, 'created_by' => $user->id,
                            'visit_id' => $visit->id, 'materials_approval_status' => 'none', 'content_status' => 'received',
                        ]);
                    }
                    $report->fill([
                        'client_uuid' => $row['uuid'], 'equipment_unit_id' => $unit?->id,
                        'customer_id' => $customer->id, 'date' => $row['date'],
                        'problem' => $row['problem'] ?? null, 'solution' => $row['solution'] ?? null, 'notes' => $row['notes'] ?? null,
                    ])->save();

                    // The server keeps the existing warehouse/material approval flow separate.
                    // Mobile free-text material lines are report fields, never automatic stock issues.
                    $fields = [];
                    foreach ($row['fields'] as $index => $field) {
                        if ($field['type'] === 'images') {
                            $existingField = $report->fields()->whereKey($field['id'] ?? 0)->where('type', 'images')->first();
                            abort_unless($existingField, 422);
                            $field['answer'] = $existingField->answer;
                        }
                        $fields[] = [
                            'question' => $field['question'], 'question_en' => $field['question_en'] ?? null,
                            'type' => $field['type'], 'options' => $field['options'] ?? null,
                            'answer' => $field['answer'] ?? null, 'order' => $index,
                            'field_key' => $field['field_key'] ?? null, 'section' => $field['section'] ?? null,
                        ];
                    }
                    $photos = [];
                    $existingPhotos = collect($fields)->where('type', 'images')->flatMap(fn ($field) => collect(json_decode($field['answer'] ?? '[]', true) ?: [])->map(fn ($path) => ['path' => $path, 'caption' => '']))->all();
                    foreach ($row['photos'] ?? [] as $photo) {
                        $photos[] = ['path' => $this->storeImage($photo['image']), 'caption' => $photo['caption'] ?? ''];
                    }
                    if ($photos) {
                        $fields[] = ['question' => 'صور الموقع', 'question_en' => 'Site photos', 'type' => 'images',
                            'answer' => json_encode(array_column($photos, 'path')), 'order' => count($fields), 'section' => 'photos'];
                    }
                    $report->fields()->delete();
                    $report->fields()->createMany($fields);
                    $snapshot = [
                        'customer_name' => $customer->localized_name, 'unit_name' => $unit?->localized_name ?? ($row['unit_name'] ?? ''),
                        'model' => $unit?->model?->model_number ?? ($row['model'] ?? ''), 'serial' => $unit?->serial_number ?? ($row['serial'] ?? ''),
                        'site' => $row['site'] ?? $unit?->location, 'maintenance_type' => $row['maintenance_type'],
                        'fields' => $fields, 'photos' => array_merge($existingPhotos, $photos),
                        'materials' => $report->materials()->with('material')->get()->map(fn ($material) => ['name' => $material->material?->localized_name, 'quantity' => $material->quantity])->all(),
                        'problem' => $report->problem, 'solution' => $report->solution, 'notes' => $report->notes,
                        'technician_signature' => $technicianSignature, 'customer_signature' => $customerSignature,
                    ];
                    $report->update(['technician_snapshot' => $snapshot]);
                    $pdfPath = 'maintenance-signed/'.$row['uuid'].'.pdf';
                    $this->stored[] = ['maintenance_private', $pdfPath];
                    if (! Storage::disk('maintenance_private')->put($pdfPath, app(MaintenanceReportPdf::class)->technician($report))) {
                        throw new \RuntimeException('Could not save signed report PDF.');
                    }
                    $report->update(['technician_pdf_path' => $pdfPath]);
                }
                $visit->update([
                    'check_out_at' => $data['finished_at'], 'status' => 'customer_signed',
                    'signed_at' => $customerSignature['signed_at'], 'signature_path' => $customerSignature['path'],
                ]);
                if ($contract && $data['kind'] === 'emergency') {
                    MaintenanceNotifications::checkEmergencyLimit($contract);
                }

                return $visit->load('reports');
            });
        } catch (\Throwable $exception) {
            foreach ($this->stored as [$disk, $path]) {
                try { Storage::disk($disk)->delete($path); } catch (\Throwable $cleanupError) { report($cleanupError); }
            }
            throw $exception;
        }
    }

    private function signature(array $data): array
    {
        return ['path' => $this->storeImage($data['image']), 'name' => $data['name'], 'signed_at' => $data['signed_at']];
    }

    private function storeImage(string $value): string
    {
        if (! preg_match('#^data:image/(png|jpeg);base64,([A-Za-z0-9+/=\r\n]+)$#D', $value, $match)) {
            throw ValidationException::withMessages(['photos' => __('maintenance.sync_invalid_image')]);
        }
        $bytes = base64_decode($match[2], true);
        $info = $bytes === false ? false : @getimagesizefromstring($bytes);
        if (! $info || strlen($bytes) > 1024 * 1024 || $info[0] > 4096 || $info[1] > 4096 || ! in_array($info['mime'], ['image/png', 'image/jpeg'], true)) {
            throw ValidationException::withMessages(['photos' => __('maintenance.sync_invalid_image')]);
        }
        $path = 'maintenance-reports/'.Str::uuid().($info['mime'] === 'image/png' ? '.png' : '.jpg');
        $this->stored[] = [config('filesystems.default'), $path];
        if (! Storage::put($path, $bytes)) {
            throw new \RuntimeException('Could not store report image.');
        }

        return $path;
    }
}
