<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
       // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Gate $gate)
    {
        // super-admin bypasses every permission check outright — it doesn't need every
        // permission synced onto it to stay fully open; a brand-new permission added later
        // is automatically covered too, with no PermissionSeeder re-run required.
        $gate->before(function (?\App\Models\User $user, string $ability) {
            if ($user?->hasRole('super-admin')) {
                return true;
            }
        });

        $this->registerPolicies();
    }
}
