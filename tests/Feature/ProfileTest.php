<?php

namespace Tests\Feature;

use App\Models\ApprovalRule;
use App\Models\PendingAction;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
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

    public function test_every_seeded_user_and_a_user_without_a_role_can_edit_their_own_profile(): void
    {
        User::factory()->create();

        foreach (User::all() as $user) {
            $originalHash = $user->password;
            $roleNames = $user->getRoleNames()->all();
            $this->actingAs($user)->get(route('profile.edit'))
                ->assertOk()
                ->assertViewHas('user', fn ($profileUser) => $profileUser->is($user))
                ->assertSee('action="'.route('profile.update').'"', false)
                ->assertDontSee('name="role"', false)
                ->assertDontSee('name="status"', false)
                ->assertDontSee('name="is_maintenance_manager"', false);

            $this->patch(route('profile.update'), [
                'name' => $user->name.' Updated',
                'email' => 'updated-'.$user->id.'@example.com',
                'phone' => '0791234567',
                'password' => '',
            ])->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors();

            $updatedUser = $user->fresh();
            $this->assertSame('updated-'.$user->id.'@example.com', $updatedUser->email);
            $this->assertSame('0791234567', $updatedUser->phone);
            $this->assertSame($originalHash, $updatedUser->password);
            $this->assertSame($roleNames, $updatedUser->getRoleNames()->all());
        }
    }

    public function test_profile_links_on_the_dashboard_and_module_pages_open_the_personal_profile(): void
    {
        $user = User::where('email', 'm@gmail.com')->sole();
        $this->actingAs($user);

        foreach (['dashboard', 'tenders.index'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('href="'.route('profile.edit').'"', false)
                ->assertDontSee('href="'.route('settings.users.show', $user).'"', false);
        }
    }

    public function test_changing_password_updates_login_credentials_and_never_queues_them_for_approval(): void
    {
        $user = User::where('email', 'thaer@gmail.com')->sole();
        $admin = User::where('email', 'alaa@gmail.com')->sole();
        $rule = ApprovalRule::create([
            'route_name' => 'profile.update',
            'label' => 'Profile',
            'status' => true,
            'created_by' => $admin->id,
        ]);
        $rule->approvers()->attach($admin);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'my-new-password',
            'password_confirmation' => 'my-new-password',
        ])->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('my-new-password', $user->fresh()->password));
        $this->assertFalse(ApprovalRule::isGateableRouteName('profile.update'));
        $this->assertSame(0, PendingAction::count());
        Auth::logout();
        $this->post(route('auth.authenticate'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->post(route('auth.authenticate'), ['email' => $user->email, 'password' => 'my-new-password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_profile_updates_leave_credentials_unchanged(): void
    {
        $user = User::where('email', 'thaer@gmail.com')->sole();
        $original = $user->getAttributes();
        $this->actingAs($user);
        $invalidUpdates = [
            [['email' => 'invalid-email'], 'email'],
            [['email' => 'alaa@gmail.com'], 'email'],
            [['password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password'],
            [['current_password' => 'wrong-password', 'password' => 'new-password', 'password_confirmation' => 'new-password'], 'current_password'],
            [['current_password' => 'password', 'password' => 'new-password', 'password_confirmation' => 'different-password'], 'password'],
            [['current_password' => 'password', 'password' => 'short', 'password_confirmation' => 'short'], 'password'],
        ];

        foreach ($invalidUpdates as [$fields, $error]) {
            $this->patchJson(route('profile.update'), array_merge([
                'name' => 'Should not be saved',
                'email' => $user->email,
            ], $fields))->assertUnprocessable()->assertJsonValidationErrors($error);
            $this->assertSame($original, $user->fresh()->getAttributes());
        }
    }

    public function test_profile_updates_cannot_target_another_user_or_change_privileged_fields(): void
    {
        $user = User::where('email', 'thaer@gmail.com')->sole();
        $otherUser = User::where('email', 'homran@gmail.com')->sole();
        $otherAttributes = $otherUser->getAttributes();
        $permissions = $user->getAllPermissions()->pluck('name')->all();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated name',
            'email' => $user->email,
            'id' => $otherUser->id,
            'user_id' => $otherUser->id,
            'role' => 'super-admin',
            'roles' => ['super-admin'],
            'permissions' => ['settings.users.edit'],
            'status' => false,
            'is_maintenance_manager' => true,
        ])->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors();

        $user = $user->fresh();
        $this->assertSame('Updated name', $user->name);
        $this->assertTrue($user->status);
        $this->assertFalse($user->is_maintenance_manager);
        $this->assertSame(['maintenance_staff'], $user->getRoleNames()->all());
        $this->assertSame($permissions, $user->getAllPermissions()->pluck('name')->all());
        $this->assertSame($otherAttributes, $otherUser->fresh()->getAttributes());

        $this->get(route('settings.users.index'))->assertForbidden();
        foreach ([$user, $otherUser] as $target) {
            $this->get(route('settings.users.show', $target))->assertForbidden();
            $this->get(route('settings.users.edit', $target))->assertForbidden();
            $this->put(route('settings.users.update', $target), ['role' => 'super-admin'])->assertForbidden();
        }
    }

    public function test_changing_email_clears_verification_but_keeping_it_preserves_verification(): void
    {
        $user = User::where('email', 'thaer@gmail.com')->sole();
        $user->email_verified_at = now();
        $user->save();
        $verifiedAt = $user->getRawOriginal('email_verified_at');
        $this->actingAs($user);

        $this->patch(route('profile.update'), ['name' => $user->name, 'email' => $user->email])
            ->assertSessionHasNoErrors();
        $this->assertSame($verifiedAt, $user->fresh()->getRawOriginal('email_verified_at'));

        $this->patch(route('profile.update'), ['name' => $user->name, 'email' => 'changed@example.com'])
            ->assertSessionHasNoErrors();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_guests_cannot_open_or_update_profiles(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('auth.login'));
        $this->patch(route('profile.update'), [])->assertRedirect(route('auth.login'));
    }
}
