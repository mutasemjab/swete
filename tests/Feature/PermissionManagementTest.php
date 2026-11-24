<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PermissionLabels;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
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

    public function test_all_registered_permissions_and_groups_have_arabic_and_english_names(): void
    {
        foreach (['ar', 'en'] as $locale) {
            foreach (Permission::all() as $permission) {
                $this->assertTrue(Lang::hasForLocale('permissions.'.$permission->name, $locale), $locale.': '.$permission->name);
                $group = explode('.', $permission->name)[0];
                $this->assertTrue(Lang::hasForLocale('permissions.groups.'.$group, $locale), $locale.': '.$group);
            }
        }

        app()->setLocale('ar');
        $this->assertSame('إضافة المواعيد', PermissionLabels::permission('crm.appointments.create'));
        $this->assertSame('custom.permission', PermissionLabels::permission('custom.permission'));
        $this->assertSame('custom_role', PermissionLabels::role('custom_role'));
    }

    public function test_permission_directory_and_role_editor_show_readable_names_and_affected_users(): void
    {
        $admin = User::where('email', 'alaa@gmail.com')->sole();
        $staff = User::where('email', 'thaer@gmail.com')->sole();
        $role = $staff->roles->sole();
        $this->actingAs($admin)->withSession(['locale' => 'ar']);

        $this->get(route('settings.permissions.index'))->assertOk()
            ->assertSee('إضافة المواعيد')->assertSee('إدارة علاقات العملاء (CRM)');
        $this->get(route('settings.permissions.index', ['role' => $role->id]))->assertOk()
            ->assertSee($staff->name)->assertSee('موظف صيانة')
            ->assertSee('action="'.route('settings.permissions.roles.update', $role).'"', false)
            ->assertViewHas('canEdit', true)
            ->assertViewHas('selectedPermissions', fn ($names) => in_array('maintenance.view', $names, true));
        $this->get(route('settings.permissions.index', ['user' => $staff->id]))->assertOk()
            ->assertViewHas('selectedRole', fn ($selectedRole) => $selectedRole->is($role));

        foreach (['settings.roles.create', 'settings.roles.edit', 'settings.roles.show'] as $route) {
            $this->get(route($route, $route === 'settings.roles.create' ? [] : $role))->assertOk();
        }
        $this->get(route('settings.roles.edit', $role))->assertSee('إضافة المواعيد');
        $this->get(route('settings.users.show', $staff))->assertOk()->assertSee('إضافة تقارير الصيانة');
    }

    public function test_granting_and_revoking_permissions_affects_every_user_with_the_role_only(): void
    {
        $admin = User::where('email', 'alaa@gmail.com')->sole();
        $staff = User::where('email', 'thaer@gmail.com')->sole();
        $colleague = User::factory()->create();
        $colleague->assignRole('maintenance_staff');
        $role = $staff->roles->sole();
        $original = $role->permissions->pluck('name')->all();
        $manager = User::where('email', 'homran@gmail.com')->sole();
        $managerPermissions = $manager->getAllPermissions()->pluck('name')->all();
        // Warm the permission cache before editing the role.
        $this->assertTrue($staff->can('maintenance.view'));
        $this->assertFalse($colleague->can('crm.view'));
        $changed = array_values(array_diff($original, ['maintenance.view']));
        $changed[] = 'crm.view';
        $changed[] = 'crm.appointments.create';

        $this->actingAs($admin)->put(route('settings.permissions.roles.update', $role), [
            'permissions' => $changed,
            'name' => 'super-admin',
        ])->assertRedirect(route('settings.permissions.index', ['role' => $role->id]))->assertSessionHasNoErrors();

        $this->assertSame('maintenance_staff', $role->fresh()->name);
        foreach ([$staff, $colleague] as $account) {
            $account = $account->fresh();
            $this->assertFalse($account->can('maintenance.view'));
            $this->assertTrue($account->can('crm.view'));
            $this->assertTrue($account->can('crm.appointments.create'));
            $this->assertSame(['maintenance_staff'], $account->getRoleNames()->all());
            $this->actingAs($account)->get(route('maintenance-reports.index'))->assertForbidden();
            $this->get(route('appointments.index'))->assertOk();
            $this->get(route('dashboard'))->assertViewHas('modules', fn ($modules) => isset($modules['crm']) && ! isset($modules['maintenance']));
        }
        $this->assertSame($managerPermissions, $manager->fresh()->getAllPermissions()->pluck('name')->all());

        $this->actingAs($admin)->put(route('settings.permissions.roles.update', $role), ['permissions' => $original])
            ->assertSessionHasNoErrors();
        $this->assertTrue($staff->fresh()->can('maintenance.view'));
        $this->assertFalse($colleague->fresh()->can('crm.view'));
    }

    public function test_clearing_a_role_keeps_direct_grants_and_other_roles_unchanged(): void
    {
        $admin = User::where('email', 'alaa@gmail.com')->sole();
        $staff = User::where('email', 'thaer@gmail.com')->sole();
        $staff->givePermissionTo('crm.view');
        $role = $staff->roles->sole();
        $otherRole = Role::findByName('accountant', 'web');
        $staff->assignRole($otherRole);

        $this->actingAs($admin)->put(route('settings.permissions.roles.update', $role), [])
            ->assertSessionHasNoErrors();

        $this->assertCount(0, $role->fresh()->permissions);
        $this->assertTrue($staff->fresh()->can('crm.view'));
        $this->assertTrue($staff->fresh()->can('accounting.view'));
        $this->assertFalse($staff->fresh()->can('maintenance.view'));
        $this->assertCount(2, $staff->fresh()->roles);
        $this->get(route('settings.users.show', $staff))->assertOk()
            ->assertSee(PermissionLabels::permission('crm.view'));
    }

    public function test_read_only_viewers_employees_and_guests_cannot_change_role_permissions(): void
    {
        $role = Role::findByName('maintenance_staff', 'web');
        $original = $role->permissions->pluck('name')->all();
        $this->put(route('settings.permissions.roles.update', $role), [])->assertRedirect(route('auth.login'));

        $employee = User::where('email', 'thaer@gmail.com')->sole();
        $this->actingAs($employee)->get(route('settings.permissions.index'))->assertForbidden();
        $this->put(route('settings.permissions.roles.update', $role), [])->assertForbidden();

        $viewer = User::factory()->create();
        $viewer->givePermissionTo('settings.permissions.view');
        $this->actingAs($viewer)->get(route('settings.permissions.index', ['role' => $role->id]))
            ->assertOk()->assertViewHas('canEdit', false)->assertDontSee(__('permissions_ui.save'));
        $this->put(route('settings.permissions.roles.update', $role), [])->assertForbidden();

        $editor = User::factory()->create();
        $editor->givePermissionTo('settings.roles.edit');
        $this->actingAs($editor)->put(route('settings.permissions.roles.update', $role), [])->assertForbidden();
        $this->assertSame($original, $role->fresh()->permissions->pluck('name')->all());

        $editor->givePermissionTo('settings.permissions.view');
        $this->actingAs($editor->fresh())->put(route('settings.permissions.roles.update', $role), ['permissions' => ['crm.view']])
            ->assertSessionHasNoErrors();
        $this->assertSame(['crm.view'], $role->fresh()->permissions->pluck('name')->all());
    }

    public function test_invalid_permissions_and_wrong_guards_never_change_the_role(): void
    {
        $admin = User::where('email', 'alaa@gmail.com')->sole();
        $role = Role::findByName('maintenance_staff', 'web');
        $original = $role->permissions->pluck('name')->all();
        Permission::create(['name' => 'api.secret', 'guard_name' => 'user-api']);
        $apiRole = Role::create(['name' => 'api-role', 'guard_name' => 'user-api']);
        $this->actingAs($admin);

        foreach (['missing.permission', 'api.secret'] as $invalid) {
            $this->putJson(route('settings.permissions.roles.update', $role), ['permissions' => ['crm.view', $invalid]])
                ->assertUnprocessable()->assertJsonValidationErrors('permissions.1');
            $this->assertSame($original, $role->fresh()->permissions->pluck('name')->all());
        }
        $this->put(route('settings.permissions.roles.update', $apiRole), [])->assertNotFound();
        $this->get(route('settings.permissions.index'))->assertDontSee('api.secret')->assertDontSee('api-role');
    }

    public function test_super_admin_permissions_are_explained_and_cannot_be_misleadingly_revoked(): void
    {
        $admin = User::where('email', 'alaa@gmail.com')->sole();
        $role = $admin->roles->sole();
        $original = $role->permissions->pluck('name')->all();
        $this->actingAs($admin)->get(route('settings.permissions.index', ['role' => $role->id]))
            ->assertOk()->assertViewHas('canEdit', false)->assertViewHas('isSuperAdmin', true)
            ->assertSee(__('permissions_ui.super_admin_hint'));
        $this->putJson(route('settings.permissions.roles.update', $role), [])->assertUnprocessable();
        $this->assertSame($original, $role->fresh()->permissions->pluck('name')->all());
    }
}
