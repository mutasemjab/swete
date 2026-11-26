<?php

namespace App\Console\Commands;

use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractScheduledVisit;
use App\Models\MaintenanceNotification;
use App\Support\MaintenanceNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/** Runs through the existing scheduler; selected recipients receive an inbox entry and email. */
class NotifyDueMaintenanceVisits extends Command
{
    protected $signature = 'maintenance:notify-due-scheduled-visits';

    protected $description = 'Notify selected users three days before visits and when emergency allowances are exhausted.';

    public function handle(): int
    {
        if (! \App\Support\MaintenanceFeatures::ready()) {
            $emails = \App\Models\User::where('is_maintenance_manager', true)->whereNotNull('email')->pluck('email');
            if ($emails->isNotEmpty()) {
                foreach (MaintenanceContractScheduledVisit::with('contract.customer')->needingNotification()->get() as $visit) {
                    Mail::to($emails->first())->bcc($emails->slice(1))->send(new \App\Mail\MaintenanceVisitDueMail($visit));
                    $visit->update(['notified_at' => now()]);
                }
            }
            return self::SUCCESS;
        }
        $visits = MaintenanceContractScheduledVisit::with('contract.customer')
            ->whereNull('notified_at')
            ->whereDate('scheduled_date', '>=', today())
            ->whereDate('scheduled_date', '<=', today()->addDays(3))
            ->whereHas('contract', fn ($q) => $q->where('status', 'active'))
            ->whereDoesntHave('actualVisit')->get();

        foreach ($visits as $visit) {
            $key = 'visit:'.$visit->id.':'.$visit->scheduled_date->toDateString();
            MaintenanceNotifications::record('visit_reminder', $key, [
                'contract_id' => $visit->contract_id, 'number' => $visit->contract->number,
                'date' => $visit->scheduled_date->toDateString(),
                'customer' => $visit->contract->customer?->localized_name ?? '',
            ]);
            if (! $visit->notified_at && MaintenanceNotification::where('dedupe_key', $key)->exists()) {
                $visit->update(['notified_at' => now()]);
            }
        }

        MaintenanceContract::whereNotNull('emergency_visit_limit')->each(
            fn ($contract) => MaintenanceNotifications::checkEmergencyLimit($contract)
        );

        MaintenanceNotification::whereNull('emailed_at')->with('user')->each(function ($notification) {
            if (! $notification->user?->status || ! $notification->user?->email) {
                return;
            }
            if (! DB::table('maintenance_notification_recipients')->where('user_id', $notification->user_id)->where('kind', $notification->kind)->exists()) {
                return;
            }
            try {
                DB::transaction(function () use ($notification) {
                    $locked = MaintenanceNotification::whereKey($notification->id)->lockForUpdate()->first();
                    if ($locked->emailed_at) {
                        return;
                    }
                    Mail::raw($locked->message."\n".route('maintenance-notifications.index'), function ($mail) use ($notification) {
                        $mail->to($notification->user->email)->subject(__('maintenance.notifications'));
                    });
                    $locked->update(['emailed_at' => now()]);
                });
            } catch (\Throwable $exception) {
                report($exception);
                $this->error('Notification '.$notification->id.' could not be emailed; retained for retry.');
            }
        });

        return self::SUCCESS;
    }
}
