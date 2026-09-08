<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\PurchaseRequestApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $count = 0;

            if (Auth::check()) {
                $count = Approval::where('approver_id', Auth::id())->where('status', 'pending')->count()
                    + PurchaseRequestApproval::where('user_id', Auth::id())->where('decision', 'pending')->count();
            }

            $view->with('pendingApprovalsCount', $count);
        });
    }
}
