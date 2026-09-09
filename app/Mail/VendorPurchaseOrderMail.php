<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** The (admin-editable, per-request) email sent to a supplier when a purchase request is marked "Sent". */
class VendorPurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailBody,
    ) {
    }

    public function build(): static
    {
        return $this->subject($this->emailSubject)
            ->html(nl2br(e($this->emailBody)));
    }
}
