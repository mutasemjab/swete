<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** The (admin-editable, per-request) email sent to a supplier when a purchase request is marked "Sent". */
class VendorPurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param UploadedFile[] $files never persisted to disk — attached straight from PHP's temp upload location. */
    public function __construct(
        public string $emailSubject,
        public string $emailBody,
        public array $files = [],
    ) {
    }

    public function build(): static
    {
        $this->subject($this->emailSubject)
            ->html(nl2br(e($this->emailBody)));

        foreach ($this->files as $file) {
            $this->attach($file->getRealPath(), [
                'as'   => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
            ]);
        }

        return $this;
    }
}
