<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportTemplate;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceVisit;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeededUserAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'app.url' => 'http://localhost',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'activitylog.enabled' => false,
        ]);
        app('url')->forceRootUrl('http://localhost');
        DB::purge('sqlite');
        $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
        $this->seed([PermissionSeeder::class, UserSeeder::class]);
    }

    public function test_each_employee_sees_and_can_open_only_the_requested_modules(): void
    {
        $access = [
            'm@gmail.com' => ['tenders', 'x8', 'quotations', 'customers', 'suppliers', 'projects'],
            'thaer@gmail.com' => ['maintenance', 'quotations'],
            'labban@gmail.com' => ['accounting'],
            'roshdy@gmail.com' => ['warehouse'],
            'r@gmail.com' => ['external_purchases', 'projects', 'customers', 'suppliers'],
            'homran@gmail.com' => ['maintenance', 'quotations'],
            'khalil@gmail.com' => [],
        ];

        foreach ($access as $email => $allowed) {
            $user = User::where('email', $email)->sole();
            $this->actingAs($user);
            $this->assertCount(1, $user->roles);
            $this->assertCount(0, $user->permissions);

            if ($email !== 'khalil@gmail.com') {
                $this->get(route('dashboard'))->assertOk()->assertViewHas('modules', function ($modules) use ($allowed) {
                    return count($modules) === count($allowed) && array_diff(array_keys($modules), $allowed) === [];
                });
            }

            foreach (config('modules') as $module => $config) {
                $response = $this->get(route($config['route']));
                $this->assertSame(in_array($module, $allowed, true) ? 200 : 403, $response->status(), $email.' => '.$module);
            }
        }
    }

    public function test_create_and_edit_permissions_stay_inside_assigned_departments(): void
    {
        $grants = [
            'm@gmail.com' => ['tenders.create', 'tenders.edit', 'quotations.price_quotes.create', 'quotations.price_quotes.edit', 'clients.customers.create', 'clients.suppliers.edit', 'projects.edit'],
            'thaer@gmail.com' => ['maintenance.reports.create', 'maintenance.reports.edit', 'maintenance.contracts.create', 'maintenance.contracts.edit', 'quotations.price_quotes.create', 'quotations.price_quotes.edit'],
            'labban@gmail.com' => ['accounting.invoices.create', 'accounting.invoice_types.edit'],
            'roshdy@gmail.com' => ['warehouse.materials.create', 'warehouse.materials.edit', 'warehouse.warehouses.create', 'warehouse.warehouses.edit'],
            'r@gmail.com' => ['external_purchases.purchase_requests.create', 'external_purchases.purchase_requests.edit', 'projects.edit', 'clients.customers.create', 'clients.suppliers.edit'],
        ];
        foreach ($grants as $email => $permissions) {
            $user = User::where('email', $email)->sole();
            foreach ($permissions as $permission) {
                $this->assertTrue($user->can($permission), $email.' => '.$permission);
            }
            $this->assertFalse($user->can('settings.users.edit'));
        }

        $accountant = User::where('email', 'labban@gmail.com')->sole();
        $this->actingAs($accountant)->get(route('accounting.customers.export'))->assertForbidden();
        $this->get(route('accounting.suppliers.create'))->assertForbidden();
        $this->get(route('settings.currencies.index'))->assertForbidden();
        $this->actingAs(User::where('email', 'r@gmail.com')->sole())->get(route('accounting.customers.create'))->assertOk();
        $this->get(route('accounting.suppliers.create'))->assertOk();
    }

    public function test_manager_has_staff_access_plus_the_maintenance_manager_workflow(): void
    {
        $staff = User::where('email', 'thaer@gmail.com')->sole();
        $manager = User::where('email', 'homran@gmail.com')->sole();
        $this->assertFalse($staff->is_maintenance_manager);
        $this->assertTrue($manager->is_maintenance_manager);
        $this->assertEqualsCanonicalizing(
            ['maintenance.requests.manage', 'maintenance.visits.manage'],
            $manager->getAllPermissions()->pluck('name')->diff($staff->getAllPermissions()->pluck('name'))->values()->all()
        );
        $this->assertEmpty($staff->getAllPermissions()->pluck('name')->diff($manager->getAllPermissions()->pluck('name')));

        $customer = Customer::create(['code' => 'TEST-CUSTOMER', 'name' => 'Test customer']);
        $request = MaintenanceRequest::create(['customer_id' => $customer->id, 'description' => 'Test request', 'status' => 'pending']);
        $this->actingAs($staff)->post(route('maintenance-requests.approve', $request))->assertForbidden();
        $this->actingAs($manager)->get(route('maintenance-requests.index'))->assertOk();
        $this->post(route('maintenance-requests.approve', $request))->assertRedirect();
        $this->assertSame('approved', $request->fresh()->status);
    }

    public function test_khalil_logs_into_his_portal_and_can_fill_only_his_own_visit_reports(): void
    {
        $khalil = User::where('email', 'khalil@gmail.com')->sole();
        $this->withSession(['url.intended' => route('accounting.invoices.index')])->post(route('auth.authenticate'), [
            'email' => $khalil->email, 'password' => 'password',
        ])->assertRedirect(route('maintenance-visits.mobile.create'));
        $this->get(route('dashboard'))->assertRedirect(route('maintenance-visits.mobile.create'));
        $this->get(route('maintenance-visits.mobile.create'))->assertOk();
        $this->get(route('maintenance-reports.create'))->assertForbidden();

        $customer = Customer::create(['code' => 'TEST-CUSTOMER', 'name' => 'Test customer']);
        $category = MaterialCategory::create(['name' => 'Test category']);
        $unit = Unit::create(['name' => 'Test unit']);
        $material = Material::create(['code' => 'TEST-MATERIAL', 'name' => 'Test material', 'category_id' => $category->id, 'unit_id' => $unit->id]);
        $template = MaintenanceReportTemplate::create(['name' => 'Test template', 'material_id' => $material->id, 'created_by' => $khalil->id]);
        $template->fields()->create(['question' => 'Work performed', 'type' => 'text', 'order' => 1]);

        $this->post(route('maintenance-visits.mobile.store'), ['customer_id' => $customer->id, 'check_in_at' => '2026-11-19 10:00:00'])->assertRedirect();
        $visit = MaintenanceVisit::where('created_by', $khalil->id)->sole();
        $this->postJson(route('maintenance-visits.mobile.reports.store', $visit), ['template_id' => $template->id])->assertOk();
        $report = MaintenanceReport::where('visit_id', $visit->id)->sole();
        $field = $report->fields()->sole();
        $this->patchJson(route('maintenance-visits.mobile.reports.answer', [$visit, $report]), ['field_id' => $field->id, 'answer' => 'Completed work'])->assertOk();
        $this->assertSame('Completed work', $field->fresh()->answer);

        $otherVisit = MaintenanceVisit::create(['customer_id' => $customer->id, 'created_by' => User::where('email', 'thaer@gmail.com')->sole()->id, 'check_in_at' => now(), 'status' => 'open']);
        $this->get(route('maintenance-visits.mobile.show', $otherVisit))->assertForbidden();
        $this->postJson(route('maintenance-visits.mobile.reports.store', $otherVisit), ['template_id' => $template->id])->assertForbidden();
        $this->post(route('maintenance-visits.mobile.finish', $visit))->assertRedirect();
        $this->assertSame('submitted_to_customer', $visit->fresh()->status);
    }

    public function test_reseeding_preserves_passwords_and_removes_previous_extra_grants(): void
    {
        $user = User::where('email', 'm@gmail.com')->sole();
        $user->update(['password' => Hash::make('changed-by-user')]);
        $passwordHash = $user->password;
        $user->assignRole('super-admin');
        $user->givePermissionTo('accounting.view');
        $count = User::count();

        $this->seed([PermissionSeeder::class, UserSeeder::class]);
        $user = $user->fresh();
        $this->assertSame($count, User::count());
        $this->assertSame($passwordHash, $user->password);
        $this->assertSame(['project_manager'], $user->roles->pluck('name')->all());
        $this->assertCount(0, $user->permissions);
        $this->assertFalse($user->can('accounting.view'));
        $this->assertSame(1, User::where('email', 'khalil@gmail.com')->count());
    }

    public function test_legacy_email_is_updated_without_creating_another_user(): void
    {
        $user = User::where('email', 'm@gmail.com')->sole();
        $user->update(['email' => 'm@m.com', 'password' => Hash::make('existing-password')]);
        $hash = $user->password;
        $count = User::count();

        $this->seed(UserSeeder::class);

        $this->assertSame($count, User::count());
        $this->assertSame($user->id, User::where('email', 'm@gmail.com')->sole()->id);
        $this->assertSame($hash, $user->fresh()->password);
    }
}
