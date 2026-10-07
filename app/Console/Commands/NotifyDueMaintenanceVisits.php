<?php

namespace App\Console\Commands;

use App\Mail\MaintenanceVisitDueMail;
use App\Models\MaintenanceContractScheduledVisit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/** Emails every maintenance-manager-flagged user once per due/overdue scheduled visit. Requires a real server cron running `php artisan schedule:run` to actually fire on a schedule. */
class NotifyDueMaintenanceVisits extends Command
{
    protected $signature = 'maintenance:notify-due-scheduled-visits';

    protected $description = 'Email maintenance managers about contract-scheduled visits that are due today or overdue, once per visit.';

    public function handle(): int
    {
        $managerEmails = User::where('is_maintenance_manager', true)->whereNotNull('email')->pluck('email');

        if ($managerEmails->isEmpty()) {
            $this->info('No maintenance managers to notify.');
            return self::SUCCESS;
        }

        $visits = MaintenanceContractScheduledVisit::with('contract.customer')->needingNotification()->get();

        foreach ($visits as $visit) {
            Mail::to($managerEmails->first())->bcc($managerEmails->slice(1))->send(new MaintenanceVisitDueMail($visit));
            $visit->update(['notified_at' => now()]);
        }

        $this->info("Notified managers about {$visits->count()} due scheduled visit(s).");

        return self::SUCCESS;
    }
}
