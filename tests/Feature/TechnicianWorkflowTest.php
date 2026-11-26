<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractScheduledVisit;
use App\Models\MaintenanceNotification;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceVisit;
use App\Models\User;
use App\Support\MaintenanceReportPdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TechnicianWorkflowTest extends TestCase
{
    private User $technician;
    private Customer $customer;
    private MaintenanceContract $contract;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null, 'activitylog.enabled' => false,
            'filesystems.default' => 'local', 'app.url' => 'http://localhost',
        ]);
        DB::purge('sqlite');
        app('url')->forceRootUrl('http://localhost');
        $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
        Storage::fake('local');
        Storage::fake('maintenance_private');
        Mail::fake();
        $this->travelTo(now()->setDate(2026, 11, 26)->startOfDay()->addHours(10));
        $this->technician = User::factory()->create(['status' => true]);
        foreach (['maintenance.reports.create', 'maintenance.view', 'maintenance.reports.edit', 'maintenance.reports.delete', 'maintenance.reports.review', 'maintenance.reports.approve'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $this->technician->givePermissionTo($permission);
        }
        $this->customer = Customer::create(['code' => 'TEST-CUSTOMER', 'name' => 'عميل الاختبار']);
        $this->contract = MaintenanceContract::create([
            'number' => 'TEST-CONTRACT', 'customer_id' => $this->customer->id, 'path' => 'test.pdf',
            'signed_date' => '2026-01-01', 'expiry_date' => '2026-12-31', 'status' => 'active',
            'created_by' => $this->technician->id, 'emergency_visit_limit' => 1,
        ]);
        $this->actingAs($this->technician);
    }

    public function test_existing_portal_serves_offline_shell_and_downloads_owned_open_visits(): void
    {
        $this->get(route('maintenance-visits.mobile.create'))->assertOk()->assertSee('technician.js')->assertDontSee('cdn.tailwindcss.com');
        $this->getJson(route('maintenance-visits.mobile.data'))->assertOk()->assertJsonPath('user.id', $this->technician->id)->assertJsonCount(1, 'customers');
        $this->get(route('maintenance-visits.mobile.worker'))->assertOk()->assertHeader('Content-Type', 'application/javascript');
    }

    public function test_signed_submission_is_idempotent_counts_one_visit_and_keeps_original_pdf(): void
    {
        DB::table('maintenance_notification_recipients')->insert(['user_id' => $this->technician->id, 'kind' => 'emergency_limit']);
        $data = $this->payload();
        $data['reports'][] = [...$data['reports'][0], 'uuid' => (string) Str::uuid()];
        $first = $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertOk()->assertJsonPath('ok', true);
        $second = $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertOk();
        $this->assertSame($first->json('visit_id'), $second->json('visit_id'));
        $this->assertSame(1, MaintenanceVisit::count());
        $this->assertSame(2, MaintenanceReport::count());
        $this->assertSame(1, $this->contract->emergencyVisits()->count());
        $this->assertSame(0, $this->contract->emergency_visits_remaining);
        $this->assertSame(1, MaintenanceNotification::where('kind', 'emergency_limit')->count());
        $report = MaintenanceReport::first();
        $this->assertStringStartsWith('%PDF-', Storage::disk('maintenance_private')->get($report->technician_pdf_path));
        $this->assertSame('received', $report->content_status);
        $this->assertSame('customer_signed', $report->visit->status);
        $this->get(route('maintenance-visits.mobile.signed-pdf', $report))->assertOk();
    }

    public function test_changed_retry_and_wrong_account_never_replace_a_signed_submission(): void
    {
        $data = $this->payload();
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertOk();
        $data['reports'][0]['solution'] = 'Changed after signing';
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertStatus(409);
        $this->assertSame('Original work', MaintenanceReport::first()->solution);
        $data['user_id']++;
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertForbidden();
    }

    public function test_invalid_signature_rolls_back_records_and_created_files(): void
    {
        $data = $this->payload();
        $data['customer_signature']['image'] = 'not an image';
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertStatus(422);
        $this->assertSame(0, MaintenanceVisit::count());
        $this->assertSame(0, MaintenanceReport::count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_contract_of_another_customer_is_rejected(): void
    {
        $other = Customer::create(['code' => 'OTHER', 'name' => 'Other customer']);
        $data = $this->payload();
        $data['customer_id'] = $other->id;
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertStatus(422);
        $this->assertSame(0, MaintenanceVisit::count());
    }

    public function test_foreman_then_engineer_approval_generates_combined_pdf_and_locks_all_edits(): void
    {
        $this->postJson(route('maintenance-visits.mobile.sync'), $this->payload())->assertOk();
        $report = MaintenanceReport::first();
        $originalPdf = Storage::disk('maintenance_private')->get($report->technician_pdf_path);
        $this->post(route('maintenance-reports.approve-content', $report))->assertNotFound();
        $this->post(route('maintenance-reports.send-to-engineer', $report), ['foreman_note' => 'Reviewed'])->assertRedirect();
        $this->post(route('maintenance-reports.approve-content', $report))->assertRedirect();
        $report->refresh();
        $this->assertSame('approved', $report->content_status);
        $this->assertSame($originalPdf, Storage::disk('maintenance_private')->get($report->technician_pdf_path));
        $parser = new \Smalot\PdfParser\Parser();
        $originalPages = count($parser->parseContent($originalPdf)->getPages());
        $approvedPages = count($parser->parseContent(Storage::disk('maintenance_private')->get($report->approved_pdf_path))->getPages());
        $this->assertGreaterThan($originalPages, $approvedPages);
        $this->putJson(route('maintenance-reports.update', $report), ['solution' => 'Alteration'])->assertForbidden();
        $this->deleteJson(route('maintenance-reports.destroy', $report))->assertForbidden();
        $this->get(route('maintenance-reports.pdf', $report))->assertOk();
    }

    public function test_pdf_failure_does_not_mark_report_approved(): void
    {
        $this->postJson(route('maintenance-visits.mobile.sync'), $this->payload())->assertOk();
        $report = MaintenanceReport::first();
        $report->update(['content_status' => 'engineer']);
        $this->mock(MaintenanceReportPdf::class, fn ($mock) => $mock->shouldReceive('approved')->andThrow(new \RuntimeException('PDF unavailable')));
        $this->postJson(route('maintenance-reports.approve-content', $report))->assertStatus(500);
        $this->assertSame('engineer', $report->fresh()->content_status);
        $this->assertNull($report->fresh()->approved_at);
    }

    public function test_reminders_start_three_days_before_and_target_only_selected_users_once(): void
    {
        // Mail::raw uses raw messages, so mock the mailer rather than Laravel's mailable fake.
        Mail::shouldReceive('raw')->once();
        DB::table('maintenance_notification_recipients')->insert(['user_id' => $this->technician->id, 'kind' => 'visit_reminder']);
        MaintenanceContractScheduledVisit::create(['contract_id' => $this->contract->id, 'scheduled_date' => '2026-11-29', 'type' => 'periodic']);
        MaintenanceContractScheduledVisit::create(['contract_id' => $this->contract->id, 'scheduled_date' => '2026-11-30', 'type' => 'periodic']);
        MaintenanceContractScheduledVisit::create(['contract_id' => $this->contract->id, 'scheduled_date' => '2026-11-25', 'type' => 'periodic']);
        MaintenanceContractScheduledVisit::create(['contract_id' => $this->contract->id, 'scheduled_date' => '2026-11-28', 'type' => 'periodic', 'notified_at' => now()->subDay()]);
        $this->artisan('maintenance:notify-due-scheduled-visits')->assertExitCode(0);
        $this->artisan('maintenance:notify-due-scheduled-visits')->assertExitCode(0);
        $this->assertSame(1, MaintenanceNotification::count());
        $this->assertSame($this->technician->id, MaintenanceNotification::first()->user_id);
        $this->assertNotNull(MaintenanceNotification::first()->emailed_at);
    }

    public function test_notification_settings_require_permission_and_keep_recipient_lists_separate(): void
    {
        $this->putJson(route('settings.maintenance-notifications.update'), [])->assertForbidden();
        $this->technician->givePermissionTo('settings.maintenance_notifications.edit');
        $recipient = User::factory()->create(['status' => true]);
        $this->put(route('settings.maintenance-notifications.update'), [
            'visit_reminder' => [$this->technician->id], 'emergency_limit' => [$recipient->id],
        ])->assertRedirect();
        $this->assertDatabaseHas('maintenance_notification_recipients', ['kind' => 'visit_reminder', 'user_id' => $this->technician->id]);
        $this->assertDatabaseHas('maintenance_notification_recipients', ['kind' => 'emergency_limit', 'user_id' => $recipient->id]);
        $this->get(route('settings.maintenance-notifications.index'))->assertOk();
    }

    public function test_periodic_visits_do_not_consume_emergency_allowance(): void
    {
        $data = $this->payload();
        $data['kind'] = 'periodic';
        $data['reports'][0]['maintenance_type'] = 'Preventive';
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertOk();
        $this->assertSame(0, $this->contract->emergencyVisits()->count());
        $this->assertSame(1, $this->contract->emergency_visits_remaining);
    }

    private function payload(): array
    {
        $image = imagecreatetruecolor(120, 40);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        imageline($image, 5, 20, 110, 25, imagecolorallocate($image, 0, 0, 0));
        ob_start(); imagepng($image); $png = ob_get_clean(); imagedestroy($image);
        $signature = ['name' => 'Test signer', 'signed_at' => now()->toISOString(), 'image' => 'data:image/png;base64,'.base64_encode($png)];

        return [
            'uuid' => (string) Str::uuid(), 'user_id' => $this->technician->id,
            'customer_id' => $this->customer->id, 'contract_id' => $this->contract->id, 'kind' => 'emergency',
            'check_in_at' => now()->subHour()->toISOString(), 'finished_at' => now()->toISOString(),
            'technician_signature' => $signature, 'customer_signature' => $signature,
            'reports' => [[
                'uuid' => (string) Str::uuid(), 'title' => 'AHU / وحدة معالجة الهواء', 'date' => today()->toDateString(),
                'maintenance_type' => 'Emergency', 'solution' => 'Original work',
                'fields' => [['field_key' => 'voltage', 'question' => 'الفولتية', 'question_en' => 'Voltage', 'type' => 'number', 'answer' => '220']],
                'photos' => [],
            ]],
        ];
    }

    public function test_completed_scheduled_visit_is_preserved_when_contract_schedule_changes(): void
    {
        $schedule = $this->contract->schedules()->create([
            'scope' => 'general', 'mode' => 'manual', 'manual_dates' => ['2026-11-26'],
            'start_date' => '2026-11-26', 'include_new_units' => true, 'is_active' => true,
        ]);
        $scheduled = $this->contract->scheduledVisits()->create([
            'schedule_id' => $schedule->id, 'source' => 'general', 'type' => 'emergency', 'scheduled_date' => '2026-11-26',
        ]);
        $data = $this->payload();
        $data['scheduled_visit_id'] = $scheduled->id;
        $this->postJson(route('maintenance-visits.mobile.sync'), $data)->assertOk();
        $this->assertFalse($scheduled->fresh()->isUntouched());
        $this->contract->update(['status' => 'suspended']);
        \App\Support\ContractVisitSync::sync($this->contract);
        $this->assertDatabaseHas('maintenance_contract_scheduled_visits', ['id' => $scheduled->id]);
    }

    public function test_schedule_opt_out_does_not_include_new_units_and_manual_dates_stay_in_contract_window(): void
    {
        $category = \App\Models\EquipmentCategory::create(['name' => 'Test category']);
        $manufacturer = \App\Models\EquipmentManufacturer::create(['name' => 'Test manufacturer']);
        $model = \App\Models\EquipmentModel::create(['equipment_category_id' => $category->id, 'equipment_manufacturer_id' => $manufacturer->id, 'model_number' => 'TEST']);
        $units = collect(['Unit 1', 'Unit 2'])->map(fn ($name) => \App\Models\EquipmentUnit::create([
            'name' => $name, 'customer_id' => $this->customer->id, 'equipment_category_id' => $category->id,
            'equipment_model_id' => $model->id, 'status' => 'operational', 'is_active' => true,
        ]));
        foreach ($units as $unit) $this->contract->contractUnits()->create(['equipment_unit_id' => $unit->id, 'added_at' => now()]);
        $schedule = $this->contract->schedules()->create([
            'scope' => 'general', 'mode' => 'manual', 'manual_dates' => ['2026-11-29', '2027-01-01'],
            'start_date' => '2026-11-26', 'include_new_units' => false, 'is_active' => true,
        ]);
        $schedule->coveredUnits()->attach($units[0]);
        \App\Support\ContractVisitSync::sync($this->contract);
        \App\Support\ContractVisitSync::sync($this->contract);
        $this->assertSame(1, $this->contract->scheduledVisits()->count());
        $this->assertSame($units[0]->id, $this->contract->scheduledVisits()->first()->equipment_unit_id);
        $this->assertSame(['2026-11-29'], $schedule->plannedDates());
        $schedule->start_date = '2027-02-01';
        $this->assertSame([], $schedule->plannedDates());
    }

    public function test_manual_text_lookup_keeps_page_numbers_and_never_fabricates_missing_text(): void
    {
        $type = \App\Models\DocumentType::create(['name' => 'Manual']);
        $document = \App\Models\Document::create([
            'document_type_id' => $type->id, 'name' => 'Controller manual', 'language' => 'en',
            'path' => 'test-manual.pdf', 'original_name' => 'manual.pdf', 'size' => 100,
            'uploaded_by' => $this->technician->id, 'is_archived' => false,
        ]);
        $pdf = new \Mpdf\Mpdf(['tempDir' => storage_path('app/maintenance-pdf-temp')]);
        $pdf->WriteHTML('P123 Controller setting, as supplied in this test manual.');
        Storage::put($document->path, $pdf->Output('', 'S'));
        $this->getJson(route('maintenance-visits.mobile.document-text', $document))->assertOk()
            ->assertJsonPath('pages.0.number', 1)->assertJsonFragment(['text' => 'P123 Controller setting, as supplied in this test manual.']);
        $document->update(['is_archived' => true]);
        $this->getJson(route('maintenance-visits.mobile.document', $document))->assertNotFound();
    }

    public function test_failed_schedule_replacement_keeps_the_previous_schedule_active(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'maintenance.contract_schedules.create', 'guard_name' => 'web']);
        $this->technician->givePermissionTo($permission);
        $previous = $this->contract->schedules()->create([
            'scope' => 'general', 'mode' => 'manual', 'manual_dates' => ['2026-11-29'],
            'start_date' => '2026-11-26', 'include_new_units' => true, 'is_active' => true,
        ]);
        \App\Models\MaintenanceContractSchedule::creating(function () { throw new \RuntimeException('Schedule write failed'); });
        try {
            $this->postJson(route('maintenance-contract-schedules.store', $this->contract), [
                'scope' => 'general', 'mode' => 'manual', 'manual_dates_text' => '2026-11-30',
                'start_date' => '2026-11-26', 'include_new_units' => true,
            ])->assertStatus(500);
            $this->assertTrue($previous->fresh()->is_active);
            $this->assertSame(1, $this->contract->schedules()->count());
        } finally {
            \App\Models\MaintenanceContractSchedule::flushEventListeners();
        }
    }
}
