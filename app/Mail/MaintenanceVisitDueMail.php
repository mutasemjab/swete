<?php

namespace App\Mail;

use App\Models\MaintenanceContractScheduledVisit;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaintenanceVisitDueMail extends Mailable
{
    use SerializesModels;

    public function __construct(public MaintenanceContractScheduledVisit $scheduledVisit)
    {
    }

    public function build(): static
    {
        return $this->subject(__('maintenance.visit_due_email_subject', [
            'number' => $this->scheduledVisit->contract->number,
        ]))->view('emails.maintenance-visit-due');
    }
}
