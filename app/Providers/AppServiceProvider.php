<?php

namespace App\Providers;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('pendingApprovalsCount', Auth::check()
                ? Approval::where('approver_id', Auth::id())->where('status', 'pending')->count()
                : 0);
        });
    }
}
