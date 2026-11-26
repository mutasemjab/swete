<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\AlarmCode;
use App\Models\Customer;
use App\Models\Document;
use App\Models\EquipmentUnit;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractScheduledVisit;
use App\Models\MaintenanceReportTemplate;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceVisit;
use App\Support\TechnicianSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TechnicianController extends Controller
{
    public function worker()
    {
        $files = array_merge(glob(base_path('assets/maintenance/*.js')), glob(base_path('assets/maintenance/*.css')), glob(base_path('assets/maintenance/reference/*.js')));
        sort($files);
        $hash = hash_init('sha256');
        foreach ($files as $file) {
            hash_update_file($hash, $file);
        }
        $version = substr(hash_final($hash), 0, 16);

        return response("self.ASEE_BUILD = '".$version."';\n".file_get_contents(base_path('assets/maintenance/worker.js')))
            ->header('Content-Type', 'application/javascript')->header('Cache-Control', 'no-cache');
    }

    public function index(Request $request, ?MaintenanceVisit $maintenanceVisit = null)
    {
        if (! \App\Support\MaintenanceFeatures::ready()) {
            $legacy = app(MobileVisitController::class);
            return $maintenanceVisit ? $legacy->show($request, $maintenanceVisit) : $legacy->create($request);
        }
        if ($maintenanceVisit) {
            abort_unless($maintenanceVisit->created_by === $request->user()->id, 403);
        }

        return response()->view('maintenance.visits.technician', [
            'initialVisitId' => $maintenanceVisit?->id,
        ])->header('X-Maintenance-User', (string) $request->user()->id)->header('Cache-Control', 'private, no-store');
    }

    public function data(Request $request)
    {
        $userId = $request->user()->id;
        $visits = MaintenanceVisit::where('created_by', $userId)->where('status', 'open')
            ->with(['reports.fields', 'reports.equipmentUnit'])->get();

        return response()->json([
            'user' => ['id' => $userId, 'name' => $request->user()->name], 'csrf' => csrf_token(),
            'customers' => Customer::where('status', true)->orderBy('name')->get()->map(fn ($c) => ['id' => $c->id, 'name' => $c->localized_name]),
            'units' => EquipmentUnit::with(['model.manufacturer', 'category'])->where('is_active', true)->get()->map(fn ($u) => [
                'id' => $u->id, 'customer_id' => $u->customer_id, 'name' => $u->localized_name,
                'category_id' => $u->equipment_category_id, 'subtype_id' => $u->equipment_subtype_id,
                'model_id' => $u->equipment_model_id, 'manufacturer_id' => $u->model?->equipment_manufacturer_id,
                'model' => $u->model?->model_number, 'manufacturer' => $u->model?->manufacturer?->localized_name,
                'category' => $u->category?->localized_name, 'serial' => $u->serial_number, 'site' => $u->location,
            ]),
            'contracts' => MaintenanceContract::with('activeUnits')->where('status', 'active')->get()->map(fn ($c) => [
                'id' => $c->id, 'customer_id' => $c->customer_id, 'number' => $c->number,
                'start' => $c->signed_date->toDateString(), 'end' => $c->expiry_date->toDateString(),
                'unit_ids' => $c->activeUnits->pluck('equipment_unit_id'),
                'limit' => $c->emergency_visit_limit, 'used' => $c->emergencyVisits()->count(),
            ]),
            'schedules' => MaintenanceContractScheduledVisit::with('contract')
                ->whereHas('contract', fn ($q) => $q->where('status', 'active'))
                ->whereDoesntHave('actualVisit')
                ->where(fn ($q) => $q->whereNull('schedule_id')->orWhereHas('schedule', fn ($s) => $s->whereNull('technician_id')->orWhere('technician_id', $userId)))
                ->orderBy('scheduled_date')->get()->map(fn ($s) => [
                    'id' => $s->id, 'contract_id' => $s->contract_id, 'customer_id' => $s->contract->customer_id,
                    'unit_id' => $s->equipment_unit_id, 'date' => $s->scheduled_date->toDateString(), 'kind' => $s->type,
                ]),
            'requests' => MaintenanceRequest::where('status', 'approved')->whereNull('linked_visit_id')->get(['id', 'customer_id', 'description']),
            'templates' => MaintenanceReportTemplate::with('fields')->where('status', true)->get()->map(fn ($t) => [
                'id' => $t->id, 'name' => $t->localized_name,
                'fields' => $t->fields->map(fn ($f) => ['field_key' => 'template_'.$f->id, 'question' => $f->question, 'question_en' => $f->question_en, 'type' => $f->type, 'options' => $f->options ?? [], 'answer' => '']),
            ]),
            'documents' => Document::with('assignments')->where('is_archived', false)->get()->map(fn ($d) => [
                'id' => $d->id, 'name' => $d->localized_name, 'tags' => $d->tags, 'filename' => $d->original_name,
                'size' => $d->size, 'version' => $d->updated_at->toISOString(),
                'assignments' => $d->assignments->map(fn ($assignment) => $assignment->only(['level', 'equipment_unit_id', 'equipment_model_id', 'equipment_subtype_id', 'equipment_category_id', 'equipment_manufacturer_id'])),
                'file_available' => (bool) $d->path,
                'url' => $d->path ? route('maintenance-visits.mobile.document', $d) : null,
            ]),
            'open_visits' => $visits->map(fn ($v) => [
                'id' => $v->id, 'customer_id' => $v->customer_id, 'contract_id' => $v->contract_id,
                'maintenance_request_id' => $v->maintenance_request_id, 'check_in_at' => $v->check_in_at->toISOString(),
                'reports' => $v->reports->map(fn ($r) => [
                    'id' => $r->id, 'title' => $r->template_name, 'template_id' => $r->template_id,
                    'equipment_unit_id' => $r->equipment_unit_id, 'date' => $r->date->toDateString(),
                    'problem' => $r->problem, 'solution' => $r->solution, 'notes' => $r->notes,
                    'base_updated_at' => $r->updated_at->toISOString(), 'content_status' => $r->content_status,
                    'fields' => $r->fields->map(fn ($f) => $f->only(['id', 'field_key', 'section', 'question', 'question_en', 'type', 'options', 'answer'])),
                ]),
            ]),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function submit(Request $request, TechnicianSubmission $submissions)
    {
        abort_unless((int) $request->input('user_id') === $request->user()->id, 403);
        abort_if(strlen($request->getContent()) > 7 * 1024 * 1024, 413);
        $data = $request->validate([
            'uuid' => ['required', 'uuid'], 'user_id' => ['required', 'integer'],
            'existing_visit_id' => ['nullable', 'integer'], 'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'contract_id' => ['nullable', 'integer'], 'scheduled_visit_id' => ['nullable', 'integer'],
            'maintenance_request_id' => ['nullable', 'integer'], 'kind' => ['required', Rule::in(['periodic', 'emergency'])],
            'check_in_at' => ['required', 'date'], 'finished_at' => ['required', 'date', 'after_or_equal:check_in_at'],
            'reports' => ['required', 'array', 'min:1', 'max:30'],
            'reports.*.uuid' => ['required', 'uuid', 'distinct'], 'reports.*.existing_report_id' => ['nullable', 'integer', 'distinct'],
            'reports.*.base_updated_at' => ['nullable', 'string'], 'reports.*.template_id' => ['nullable', 'integer'],
            'reports.*.equipment_unit_id' => ['nullable', 'integer'], 'reports.*.title' => ['required', 'string', 'max:200'],
            'reports.*.date' => ['required', 'date_format:Y-m-d'],
            'reports.*.maintenance_type' => ['required', Rule::in(['Startup', 'Preventive', 'Corrective', 'Emergency', 'Commissioning'])],
            'reports.*.problem' => ['nullable', 'string', 'max:20000'], 'reports.*.solution' => ['nullable', 'string', 'max:20000'],
            'reports.*.notes' => ['nullable', 'string', 'max:20000'],
            'reports.*.unit_name' => ['nullable', 'string', 'max:200'], 'reports.*.model' => ['nullable', 'string', 'max:200'],
            'reports.*.serial' => ['nullable', 'string', 'max:200'], 'reports.*.site' => ['nullable', 'string', 'max:300'],
            'reports.*.fields' => ['present', 'array', 'max:300'], 'reports.*.fields.*.id' => ['nullable', 'integer'],
            'reports.*.fields.*.field_key' => ['nullable', 'string', 'max:150'], 'reports.*.fields.*.section' => ['nullable', 'string', 'max:100'],
            'reports.*.fields.*.question' => ['required', 'string', 'max:255'], 'reports.*.fields.*.question_en' => ['nullable', 'string', 'max:255'],
            'reports.*.fields.*.type' => ['required', Rule::in(['text', 'number', 'boolean', 'choice', 'images'])],
            'reports.*.fields.*.options' => ['nullable', 'array', 'max:100'], 'reports.*.fields.*.options.*' => ['string', 'max:255'],
            'reports.*.fields.*.answer' => ['nullable', 'string', 'max:20000'],
            'reports.*.photos' => ['nullable', 'array', 'max:12'], 'reports.*.photos.*.image' => ['required', 'string', 'max:1400000'],
            'reports.*.photos.*.caption' => ['nullable', 'string', 'max:500'],
            'technician_signature' => ['required', 'array'], 'customer_signature' => ['required', 'array'],
            'technician_signature.image' => ['required', 'string', 'max:1400000'], 'customer_signature.image' => ['required', 'string', 'max:1400000'],
            'technician_signature.name' => ['required', 'string', 'max:150'], 'customer_signature.name' => ['required', 'string', 'max:150'],
            'technician_signature.signed_at' => ['required', 'date'], 'customer_signature.signed_at' => ['required', 'date'],
        ]);
        $visit = $submissions->submit($request->user(), $data);

        return response()->json(['ok' => true, 'visit_id' => $visit->id, 'reports' => $visit->reports->map(fn ($r) => [
            'uuid' => $r->client_uuid, 'id' => $r->id, 'number' => $r->number,
            'pdf_url' => route('maintenance-visits.mobile.signed-pdf', $r),
        ])]);
    }

    public function document(Document $document)
    {
        abort_if($document->is_archived || ! $document->path, 404);

        return Storage::response($document->path, $document->original_name, ['Cache-Control' => 'private, no-store']);
    }

    public function alarms()
    {
        $reference = json_decode(file_get_contents(resource_path('maintenance/reference/alarms.json')), true, 512, JSON_THROW_ON_ERROR);
        $reference['database'] = AlarmCode::with('controlSystem')->get()->map(function ($alarm) {
            $data = $alarm->only(['code', 'key', 'name_en', 'name_ar', 'causes_en', 'causes_ar', 'checks_en', 'checks_ar', 'effect_en', 'effect_ar', 'action_en', 'action_ar', 'reset_en', 'reset_ar', 'source_document_id', 'source_page']);
            $data['controller'] = $alarm->controlSystem?->localized_name;
            $data['controller_identity'] = $alarm->controlSystem?->name_en ?: $alarm->controlSystem?->name;

            return $data;
        });

        // The imported database can contain the same catalogue bundled with the mobile app.
        // Prefer its editable, source-linked record; retain genuinely different controllers.
        $identity = function ($row) use ($reference) {
            $values = [];
            foreach ($reference['fields'] as $field) {
                $value = $field === 'controller' ? ($row['controller_identity'] ?? $row['controller'] ?? '') : ($row[$field] ?? '');
                $values[] = trim($value);
            }
            return hash('sha256', json_encode($values, JSON_UNESCAPED_UNICODE));
        };
        $databaseKeys = $reference['database']->mapWithKeys(fn ($row) => [$identity($row) => true])->all();
        $reference['alarms'] = array_values(array_filter($reference['alarms'], function ($values) use ($reference, $identity, $databaseKeys) {
            return ! isset($databaseKeys[$identity(array_combine($reference['fields'], $values))]);
        }));

        return response()->json($reference)->header('Cache-Control', 'private, no-store');
    }

    public function documentText(Document $document, \App\Support\MaintenanceDocumentText $text)
    {
        abort_if($document->is_archived, 404);

        try {
            return response()->json(['pages' => $text->pages($document)]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['pages' => [], 'unavailable' => true]);
        }
    }

    public function signedPdf(Request $request, \App\Models\MaintenanceReport $maintenanceReport)
    {
        abort_unless($maintenanceReport->created_by === $request->user()->id, 403);
        abort_unless($maintenanceReport->technician_pdf_path, 404);

        return Storage::disk('maintenance_private')->download($maintenanceReport->technician_pdf_path, $maintenanceReport->number.'-signed.pdf');
    }
}
