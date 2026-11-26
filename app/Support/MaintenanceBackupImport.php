<?php

namespace App\Support;

use App\Models\Document;
use App\Models\MaintenanceReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/** Explicit, additive import. Never restores SQL, users, roles, balances or stock. */
class MaintenanceBackupImport
{
    private array $package;
    private array $mapping;
    private array $ids = [];
    private array $createdFiles = [];
    private string $directory;
    private string $hash;
    private int $runId;
    private bool $dataOnly = false;

    /** Repository-owned PHP envelope contains compressed rows and the reviewed mapping, no binaries. */
    public function loadEmbedded(string $path): void
    {
        $bundle = require $path;
        $compressed = base64_decode($bundle['data'] ?? '', true);
        $raw = $compressed === false ? false : gzdecode($compressed);
        if ($raw === false || ! hash_equals($bundle['sha256'] ?? '', hash('sha256', $raw))) {
            throw new RuntimeException('Bundled maintenance data checksum mismatch.');
        }
        $this->hash = $bundle['sha256'];
        $this->package = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if ($this->package['version'] !== 1 || $this->package['source'] !== 'hvac') throw new RuntimeException('Unsupported maintenance data.');
        $this->mapping = $bundle['mapping'];
        $this->directory = '';
        $this->dataOnly = true;
        $this->ids = [];
        $this->createdFiles = [];
    }

    public function load(string $directory, array $mapping = []): array
    {
        $this->dataOnly = false;
        $this->directory = rtrim($directory, '/\\');
        $manifest = json_decode(file_get_contents($this->directory.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
        $raw = gzdecode(file_get_contents($this->directory.'/data.json.gz'));
        if ($raw === false || ! hash_equals($manifest['sha256'], hash('sha256', $raw))) {
            throw new RuntimeException('Maintenance package checksum mismatch.');
        }
        $this->hash = $manifest['sha256'];
        $this->package = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        unset($raw);
        if ($this->package['version'] !== 1 || $this->package['source'] !== 'hvac') {
            throw new RuntimeException('Unsupported maintenance package.');
        }
        $this->mapping = $mapping;
        $this->ids = [];
        $this->createdFiles = [];
        foreach ($this->package['files'] as $path => $file) {
            $full = $this->sourceFile($path);
            if (! is_file($full) || filesize($full) !== $file['size'] || ! hash_equals($file['sha256'], hash_file('sha256', $full))) {
                throw new RuntimeException('Missing or changed package file: '.$path);
            }
        }
        return $manifest;
    }

    /** Read-only preview. Identity matches are suggestions, never automatic customer merges. */
    public function preview(): array
    {
        $blockers = [];
        $allowedMappings = ['customers', 'equipment_categories', 'equipment_subtypes', 'equipment_manufacturers', 'equipment_models', 'equipment_units', 'control_systems', 'document_types'];
        foreach (array_keys($this->mapping['map'] ?? []) as $table) {
            if (! in_array($table, $allowedMappings, true)) $blockers[] = 'Mapping existing '.$table.' records is not supported; existing transactional records cannot be rewritten.';
        }
        if (($this->mapping['source_sha256'] ?? '') !== $this->hash) {
            $blockers[] = 'mapping.source_sha256 must equal '.$this->hash;
        }
        if (! DB::table('users')->where('id', $this->mapping['actor_user_id'] ?? 0)->exists()) {
            $blockers[] = 'Choose actor_user_id from existing users for import attribution.';
        }
        foreach ($this->rows('core_user') as $user) {
            $target = DB::table('users')->where('id', $this->mapping['users'][$user['id']] ?? 0)->first();
            if (! $target) {
                $blockers[] = 'Map source user '.$user['id'].' ('.$user['username'].') to an existing user.';
            } elseif (empty($this->mapping['user_emails'][$user['id']]) || strcasecmp($target->email, $this->mapping['user_emails'][$user['id']]) !== 0) {
                $blockers[] = 'User ID/email mismatch for source user '.$user['id'].'. Review the mapping on this database.';
            }
        }
        $candidates = [];
        foreach ($this->rows('core_client') as $row) {
            $matches = DB::table('customers')->where(function ($q) use ($row) {
                $names = array_values(array_filter([$row['name_ar'], $row['name_en']]));
                $q->whereIn('name', $names)->orWhereIn('name_en', $names)->orWhere('code', 'HVAC-'.$row['id']);
                if ($row['email']) $q->orWhere('email', $row['email']);
                if ($row['phone']) $q->orWhere('phone', $row['phone']);
            })->get(['id', 'code', 'name']);
            if ($matches->isNotEmpty()) {
                $candidates[$row['id']] = $matches->toArray();
                if (! array_key_exists($row['id'], $this->mapping['map']['customers'] ?? [])) {
                    $blockers[] = 'Review customer '.$row['id'].'; set map.customers.'.$row['id'].' to an existing ID or "create".';
                }
            }
        }
        foreach (['core_contractpayment', 'core_fielddefinition', 'core_technicalvalue', 'core_reportform'] as $table) {
            if ($this->rows($table)) $blockers[] = $table.' has rows; this package needs a reviewed mapping for that table.';
        }
        return ['sha256' => $this->hash, 'counts' => array_map('count', $this->package['tables']),
            'files' => count($this->package['files']), 'data_only' => $this->dataOnly, 'users' => $this->rows('core_user'),
            'customer_candidates' => $candidates, 'blockers' => $blockers];
    }

    public function apply(): array
    {
        $mappingHash = hash('sha256', $this->json($this->mapping));
        $previous = DB::table('maintenance_import_runs')->where('source', 'hvac')->first();
        if ($previous) {
            if ($previous->sha256 !== $this->hash || $previous->mapping_sha256 !== $mappingHash || ! $previous->completed_at) {
                throw new RuntimeException('This source already has a different import. Reconciliation requires a separate review.');
            }
            return ['already_imported' => true, 'run_id' => $previous->id];
        }
        $preview = $this->preview();
        if ($preview['blockers']) throw new RuntimeException(implode("\n", $preview['blockers']));

        try {
            return DB::transaction(function () use ($mappingHash) {
                // The unique source key prevents concurrent imports from duplicating records.
                $this->runId = DB::table('maintenance_import_runs')->insertGetId([
                    'source' => 'hvac', 'sha256' => $this->hash, 'mapping_sha256' => $mappingHash,
                ]);
                foreach ($this->rows('core_user') as $row) {
                    $this->ids['core_user'][$row['id']] = (int) $this->mapping['users'][$row['id']];
                }
                $this->referenceData();
                $this->documents();
                $this->contracts();
                $this->reports();
                $this->visitHistory();
                // Preserve unrepresented source fields and links without applying old settings,
                // notifications, financial amounts or permissions to the current application.
                foreach ($this->package['tables'] as $table => $rows) {
                    if ($table === 'core_documentpage') continue; // stored during document import
                    foreach ($rows as $row) {
                        if (! DB::table('maintenance_import_records')->where('run_id', $this->runId)->where('source_table', $table)->where('source_id', $row['id'])->exists()) {
                            $this->record($table, $row);
                        }
                    }
                }
                DB::table('maintenance_import_runs')->where('id', $this->runId)->update(['completed_at' => now()]);
                return ['already_imported' => false, 'run_id' => $this->runId, 'mapped' => array_map('count', $this->ids)];
            });
        } catch (\Throwable $exception) {
            foreach ($this->createdFiles as [$disk, $path]) {
                try { Storage::disk($disk)->delete($path); } catch (\Throwable $cleanup) { report($cleanup); }
            }
            throw $exception;
        }
    }

    private function referenceData(): void
    {
        foreach (['core_category' => 'equipment_categories', 'core_manufacturer' => 'equipment_manufacturers',
            'core_controller' => 'control_systems', 'core_documenttype' => 'document_types'] as $source => $target) {
            foreach ($this->rows($source) as $r) {
                $data = $this->name($r) + ['status' => $this->bool($r['is_active'] ?? 't')];
                if ($source === 'core_manufacturer') $data['website'] = $r['website'] ?: null;
                $this->put($source, $r, $target, $data, ['name' => $data['name']]);
            }
        }
        foreach ($this->rows('core_subtype') as $r) {
            $data = $this->name($r) + ['equipment_category_id' => $this->id('core_category', $r['category_id']), 'status' => $this->bool($r['is_active'])];
            $this->put('core_subtype', $r, 'equipment_subtypes', $data, array_intersect_key($data, array_flip(['equipment_category_id', 'name'])));
        }
        foreach ($this->rows('core_client') as $r) {
            $this->put('core_client', $r, 'customers', $this->name($r) + [
                'code' => 'HVAC-'.$r['id'], 'phone' => $r['phone'] ?: null, 'email' => $r['email'] ?: null,
                'address' => $r['address'] ?: null, 'status' => $this->bool($r['is_active']), 'created_at' => $this->date($r['created_at']),
            ]);
        }
        foreach ($this->rows('core_equipmentmodel') as $r) {
            $data = ['equipment_manufacturer_id' => $this->id('core_manufacturer', $r['manufacturer_id']),
                'equipment_category_id' => $this->id('core_category', $r['category_id']),
                'equipment_subtype_id' => $this->id('core_subtype', $r['subtype_id']),
                'control_system_id' => $this->id('core_controller', $r['controller_id']),
                'model_number' => $r['model_number'], 'status' => $this->bool($r['is_active']), 'reading_limits' => $r['reading_limits']];
            $this->put('core_equipmentmodel', $r, 'equipment_models', $data, array_intersect_key($data, array_flip(['equipment_manufacturer_id', 'model_number'])));
        }
        foreach ($this->rows('core_unit') as $r) {
            if (! in_array($r['status'], \App\Models\EquipmentUnit::STATUSES, true)) throw new RuntimeException('Unknown source unit status.');
            $data = $this->name($r) + ['customer_id' => $this->id('core_client', $r['client_id']),
                'equipment_category_id' => $this->id('core_category', $r['category_id']),
                'equipment_subtype_id' => $this->id('core_subtype', $r['subtype_id']),
                'equipment_model_id' => $this->id('core_equipmentmodel', $r['model_id']),
                'serial_number' => $r['serial_number'], 'location' => $r['location'], 'project' => $r['project'],
                'status' => $r['status'], 'is_active' => $this->bool($r['is_active']), 'notes' => $r['notes'],
                'qr_token' => $r['qr_token'], 'reading_limits' => $r['reading_limits'], 'created_at' => $this->date($r['created_at'])];
            $this->put('core_unit', $r, 'equipment_units', $data, array_intersect_key($data, array_flip(['customer_id', 'name'])));
        }
    }

    private function documents(): void
    {
        foreach ($this->rows('core_document') as $r) {
            $this->put('core_document', $r, 'documents', $this->name($r) + [
                'document_type_id' => $this->id('core_documenttype', $r['doc_type_id']), 'language' => $r['language'],
                'revision' => $r['revision'], 'tags' => $r['tags'], 'path' => $this->dataOnly ? '' : $this->copyFile($r['file']),
                'original_name' => $r['original_name'], 'content_type' => $r['content_type'], 'size' => (int) $r['size'],
                'uploaded_by' => $this->id('core_user', $r['uploaded_by_id']), 'is_archived' => $this->bool($r['is_archived']),
                'created_at' => $this->date($r['uploaded_at']), 'updated_at' => $this->date($r['updated_at']),
            ]);
        }
        foreach ($this->rows('core_documentassignment') as $r) {
            $data = ['document_id' => $this->id('core_document', $r['document_id']), 'level' => $r['level'],
                'created_by' => $this->id('core_user', $r['created_by_id']), 'created_at' => $this->date($r['created_at'])];
            foreach (['unit' => 'core_unit', 'model' => 'core_equipmentmodel', 'subtype' => 'core_subtype', 'category' => 'core_category', 'manufacturer' => 'core_manufacturer'] as $kind => $table) {
                $data['equipment_'.$kind.'_id'] = $this->id($table, $r[$kind.'_id']);
            }
            $this->put('core_documentassignment', $r, 'document_assignments', $data);
        }
        foreach ($this->rows('core_alarmcode') as $r) {
            $data = array_intersect_key($r, array_flip(['code', 'key', 'section', 'name_en', 'name_ar', 'causes_en', 'causes_ar', 'checks_en', 'checks_ar', 'effect_en', 'effect_ar', 'action_en', 'action_ar', 'reset_en', 'reset_ar', 'source_page']));
            $data += ['control_system_id' => $this->id('core_controller', $r['controller_id']), 'source_document_id' => $this->id('core_document', $r['source_document_id'])];
            // Manuals reuse a code/name for different equipment sections and pages.
            $this->put('core_alarmcode', $r, 'alarm_codes', $data, array_intersect_key($data, array_flip(['control_system_id', 'key', 'source_document_id', 'name_en', 'section', 'source_page'])));
        }
        $pages = [];
        foreach ($this->rows('core_documentpage') as $r) {
            if ($this->dataOnly) {
                $this->record('core_documentpage', $r, 'documents', $this->id('core_document', $r['document_id']));
            } else {
                $pages[$r['document_id']][] = ['number' => (int) $r['number'], 'text' => $r['text']];
            }
        }
        foreach ($pages as $sourceId => $rows) {
            usort($rows, fn ($a, $b) => $a['number'] <=> $b['number']);
            $document = Document::findOrFail($this->id('core_document', $sourceId));
            $key = MaintenanceDocumentText::cacheKey($document);
            $this->storeBytes('maintenance_private', $key, $this->json($rows));
        }
    }

    private function contracts(): void
    {
        foreach ($this->rows('core_contract') as $r) {
            $this->put('core_contract', $r, 'maintenance_contracts', [
                'number' => $r['number'], 'customer_id' => $this->id('core_client', $r['client_id']),
                'path' => '', 'signed_date' => $r['start_date'], 'expiry_date' => $r['end_date'], 'status' => $r['status'],
                'notes' => $r['notes'], 'emergency_visit_limit' => $r['emergency_visits'],
                'created_by' => $this->mapping['actor_user_id'], 'created_at' => $this->date($r['created_at']),
            ], ['number' => $r['number']]);
        }
        foreach ($this->rows('core_contractequipment') as $r) {
            $this->put('core_contractequipment', $r, 'maintenance_contract_units', [
                'contract_id' => $this->id('core_contract', $r['contract_id']), 'equipment_unit_id' => $this->id('core_unit', $r['unit_id']),
                'added_at' => $this->date($r['added_at']), 'removed_at' => $this->date($r['removed_at']),
            ]);
        }
        foreach ($this->rows('core_schedule') as $r) {
            $this->put('core_schedule', $r, 'maintenance_contract_schedules', [
                'contract_id' => $this->id('core_contract', $r['contract_id']), 'scope' => $r['scope'],
                'equipment_unit_id' => $this->id('core_unit', $r['unit_id']), 'mode' => $r['mode'],
                'interval_value' => $r['interval_value'], 'interval_unit' => $r['interval_unit'], 'manual_dates' => $r['manual_dates'],
                'start_date' => $r['start_date'], 'end_date' => $r['end_date'], 'include_new_units' => $this->bool($r['include_new_units']),
                'is_active' => $this->bool($r['is_active']), 'notes' => $r['notes'],
                'engineer_id' => $this->id('core_user', $r['engineer_id']), 'technician_id' => $this->id('core_user', $r['technician_id']),
            ]);
        }
        foreach ($this->rows('core_schedule_covered_units') as $r) {
            DB::table('maintenance_schedule_units')->insert(['schedule_id' => $this->id('core_schedule', $r['schedule_id']), 'equipment_unit_id' => $this->id('core_unit', $r['unit_id'])]);
        }
        foreach ($this->rows('core_visit') as $r) {
            if (! $r['contract_id'] || $r['status'] !== 'scheduled') continue;
            if ($r['visit_type'] !== 'preventive' && $r['visit_type'] !== 'emergency') throw new RuntimeException('Unsupported scheduled visit type.');
            $notified = collect($this->rows('core_notification'))->where('visit_id', $r['id'])->min('created_at');
            $this->put('core_visit', $r, 'maintenance_contract_scheduled_visits', [
                'contract_id' => $this->id('core_contract', $r['contract_id']), 'equipment_unit_id' => $this->id('core_unit', $r['unit_id']),
                'schedule_id' => $this->id('core_schedule', $r['schedule_id']), 'source' => $r['source'],
                'scheduled_date' => $r['scheduled_date'], 'type' => $r['visit_type'] === 'emergency' ? 'emergency' : 'periodic',
                'notes' => $r['notes'], 'notified_at' => $this->date($notified), 'created_at' => $this->date($r['created_at']),
            ]);
        }
    }

    private function reports(): void
    {
        $visits = collect($this->rows('core_visit'))->keyBy('id');
        foreach ($this->rows('core_fieldreport') as $r) {
            $visit = $visits->get($r['visit_id']);
            if (! $visit) throw new RuntimeException('Field report has no source visit; review its customer identity.');
            $data = json_decode($r['data'], true, 512, JSON_THROW_ON_ERROR);
            $visitReport = collect($this->rows('core_visitreport'))->firstWhere('visit_id', $visit['id']);
            $attachments = collect($this->rows('core_attachment'))->filter(fn ($a) => $a['visit_id'] === $visit['id'] || $a['field_report_id'] === $r['id']);
            $pdfs = $attachments->where('kind', 'report')->where('content_type', 'application/pdf');
            if ($pdfs->count() !== 1) throw new RuntimeException('Expected one original PDF for report '.$r['id']);
            $originalPath = $this->dataOnly ? null : $this->copyFile($pdfs->first()['file'], 'maintenance_private');
            if (! in_array($r['status'], ['received', 'engineer', 'approved'], true)) throw new RuntimeException('Unknown source report approval state.');
            $reportId = $this->put('core_fieldreport', $r, 'maintenance_reports', [
                'number' => 'HVAC-'.$visit['number'], 'template_name' => $r['machine_type'],
                'customer_id' => $this->id('core_client', $visit['client_id']), 'equipment_unit_id' => $this->id('core_unit', $visit['unit_id']),
                'date' => $r['report_date'], 'problem' => $visitReport['findings'] ?? '',
                'solution' => $visitReport['actions_taken'] ?? '', 'notes' => $data['general_notes'] ?? '',
                'created_by' => $this->id('core_user', $r['uploaded_by_id']) ?? $this->mapping['actor_user_id'],
                'content_status' => $r['status'], 'foreman_note' => $r['foreman_note'],
                'foreman_reviewed_by' => $this->id('core_user', $r['foreman_by_id']), 'foreman_reviewed_at' => $this->date($r['foreman_at']),
                'approved_by' => $this->id('core_user', $r['engineer_by_id']), 'approved_at' => $this->date($r['engineer_at']),
                'technician_pdf_path' => $originalPath, 'created_at' => $this->date($r['created_at']),
            ]);
            $fields = [];
            foreach ($data['checklist_items'] ?? [] as $item) {
                $fields[] = ['question' => $item['label_ar'] ?: $item['label_en'], 'question_en' => $item['label_en'],
                    'field_key' => $item['field_key'], 'type' => 'text', 'section' => 'checklist', 'answer' => (string) $item['value']];
            }
            foreach (['materials', 'attention_notes', 'required_maintenance'] as $section) {
                $items = $data[$section] ?? [];
                if (is_string($items)) $items = $items === '' ? [] : [$items];
                foreach ($items as $item) {
                    [$label, $english, $answer] = match ($section) {
                        'materials' => ['المواد', 'Materials', trim(($item['description'] ?? '').' · '.($item['qty'] ?? '').' '.($item['unit'] ?? ''))],
                        'attention_notes' => ['ملاحظات تحتاج انتباه', 'Attention notes', trim(($item['unit_no'] ?? '').' · '.($item['note'] ?? ''))],
                        default => ['الصيانة المطلوبة', 'Required maintenance', (string) $item],
                    };
                    $fields[] = ['question' => $label, 'question_en' => $english, 'type' => 'text', 'section' => $section, 'answer' => $answer];
                }
            }
            $photos = [];
            foreach ($attachments->whereIn('kind', ['photo', 'signature']) as $attachment) {
                if ($this->dataOnly) continue;
                $path = $this->copyFile($attachment['file']);
                $photos[] = ['path' => $path, 'caption' => $attachment['original_name']];
                $fields[] = ['question' => $attachment['original_name'], 'question_en' => $attachment['original_name'],
                    'type' => 'images', 'section' => $attachment['kind'], 'answer' => $this->json([$path])];
            }
            foreach ($fields as $index => $field) {
                DB::table('maintenance_report_fields')->insert($field + ['report_id' => $reportId, 'order' => $index, 'created_at' => now(), 'updated_at' => now()]);
            }
            $snapshot = ['customer_name' => $r['client_name'], 'model' => $r['machine_model'], 'serial' => $r['serial_no'], 'site' => $r['site'],
                'maintenance_type' => $r['maintenance_type'], 'fields' => $fields, 'photos' => $photos,
                'files_deferred' => $this->dataOnly];
            DB::table('maintenance_reports')->where('id', $reportId)->update(['technician_snapshot' => $this->json($snapshot)]);
            if ($r['status'] === 'approved' && ! $this->dataOnly) {
                $report = MaintenanceReport::findOrFail($reportId);
                $path = 'maintenance-approved/import-'.$this->hash.'-'.$r['id'].'.pdf';
                $this->storeBytes('maintenance_private', $path, app(MaintenanceReportPdf::class)->approved($report));
                DB::table('maintenance_reports')->where('id', $reportId)->update(['approved_pdf_path' => $path]);
            }
        }
    }

    private function visitHistory(): void
    {
        foreach ($this->rows('core_visit') as $r) {
            $record = DB::table('maintenance_import_records')->where('run_id', $this->runId)->where('source_table', 'core_visit')->where('source_id', $r['id'])->value('id');
            $record ??= $this->record('core_visit', $r);
            $report = collect($this->rows('core_fieldreport'))->firstWhere('visit_id', $r['id']);
            DB::table('maintenance_import_visits')->insert([
                'import_record_id' => $record, 'customer_id' => $this->id('core_client', $r['client_id']),
                'equipment_unit_id' => $this->id('core_unit', $r['unit_id']), 'report_id' => $report ? $this->id('core_fieldreport', $report['id']) : null,
                'number' => $r['number'], 'scheduled_date' => $r['scheduled_date'], 'actual_date' => $r['actual_date'],
                'status' => $r['status'], 'visit_type' => $r['visit_type'],
            ]);
        }
    }

    private function put(string $source, array $row, string $target, array $data, array $identity = []): int
    {
        $explicit = $this->mapping['map'][$target][$row['id']] ?? null;
        if ($explicit !== null && $explicit !== 'create') {
            if (! ctype_digit((string) $explicit) || ! DB::table($target)->where('id', $explicit)->exists()) throw new RuntimeException('Invalid mapping for '.$source.':'.$row['id']);
            $id = (int) $explicit; // Existing records are never overwritten, even when explicitly mapped.
        } else {
            $matches = $identity ? DB::table($target)->where($identity)->get() : collect();
            if ($matches->isNotEmpty()) {
                if ($explicit === 'create' || $matches->count() !== 1) throw new RuntimeException('Identity conflict in '.$target.':'.$row['id']);
                $existing = (array) $matches->first();
                foreach ($data as $key => $value) {
                    if (in_array($key, ['created_at', 'updated_at'], true)) continue;
                    if ((string) ($existing[$key] ?? '') !== (string) $value) throw new RuntimeException('Review map.'.$target.'.'.$row['id'].'; existing '.$key.' differs.');
                }
                $id = (int) $existing['id'];
            } else {
                $id = DB::table($target)->insertGetId($data + ['created_at' => now(), 'updated_at' => now()]);
            }
        }
        $this->ids[$source][$row['id']] = $id;
        $this->record($source, $row, $target, $id);
        return $id;
    }

    private function record(string $table, array $row, ?string $target = null, ?int $id = null): int
    {
        return DB::table('maintenance_import_records')->insertGetId(['run_id' => $this->runId,
            'source_table' => $table, 'source_id' => $row['id'], 'target_table' => $target, 'target_id' => $id, 'payload' => $this->json($row)]);
    }

    private function copyFile(string $path, ?string $disk = null): string
    {
        $disk ??= config('filesystems.default');
        $relative = 'files/'.preg_replace('#^files/#', '', $path);
        $source = $this->sourceFile($relative);
        $meta = $this->package['files'][$relative] ?? throw new RuntimeException('Unlisted source file.');
        if (! in_array(strtolower(pathinfo($relative, PATHINFO_EXTENSION)), ['pdf', 'png', 'jpeg', 'jpg', 'xlsx', 'docx'], true)) throw new RuntimeException('Unsupported attachment type.');
        $target = 'maintenance-imports/'.$this->hash.'/'.$relative;
        if (Storage::disk($disk)->exists($target)) {
            $stream = Storage::disk($disk)->readStream($target);
            try { $hash = hash_init('sha256'); hash_update_stream($hash, $stream); $same = hash_equals($meta['sha256'], hash_final($hash)); } finally { fclose($stream); }
            if (! $same) throw new RuntimeException('Existing imported file differs: '.$target);
        } else {
            $this->createdFiles[] = [$disk, $target];
            $stream = fopen($source, 'rb');
            try { if (! Storage::disk($disk)->put($target, $stream)) throw new RuntimeException('Could not store attachment.'); } finally { fclose($stream); }
        }
        return $target;
    }

    private function storeBytes(string $disk, string $path, string $bytes): void
    {
        if (Storage::disk($disk)->exists($path)) throw new RuntimeException('Refusing to overwrite '.$path);
        $this->createdFiles[] = [$disk, $path];
        if (! Storage::disk($disk)->put($path, $bytes)) throw new RuntimeException('Could not store imported content.');
    }

    private function sourceFile(string $relative): string
    {
        if (! preg_match('#^files/[A-Za-z0-9_./-]+$#D', $relative) || str_contains($relative, '..')) throw new RuntimeException('Unsafe package path.');
        $full = realpath($this->directory.'/'.$relative);
        $root = realpath($this->directory).DIRECTORY_SEPARATOR;
        if ($full === false || ! str_starts_with($full, $root)) throw new RuntimeException('File outside package.');
        return $full;
    }

    private function id(string $table, $id): ?int
    {
        if ($id === null || $id === '') return null;
        return $this->ids[$table][$id] ?? throw new RuntimeException('Unmapped reference '.$table.':'.$id);
    }
    private function rows(string $table): array { return $this->package['tables'][$table] ?? []; }
    private function name(array $r): array { return ['name' => $r['name_ar'] ?: $r['name_en'], 'name_en' => $r['name_en'] ?: null]; }
    private function bool($value): bool { return $value === true || $value === 't' || $value === 1; }
    private function date(?string $value): ?string { return $value ? Carbon::parse($value)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') : null; }
    private function json($value): string { return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR); }
}
