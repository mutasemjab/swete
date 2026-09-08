<?php

namespace App\Mail;

use App\Models\ShippingCompany;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShippingQuoteRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Collection $purchaseRequests one or more purchase requests bundled into this one RFQ
     * @param UploadedFile[] $files
     */
    public function __construct(
        public Collection $purchaseRequests,
        public ShippingCompany $shippingCompany,
        public ?string $message,
        public array $files,
    ) {
    }

    public function build(): static
    {
        $numbers = $this->purchaseRequests->pluck('number')->implode(', ');

        $this->subject(__('external_purchases.ship_email_subject', ['number' => $numbers]))
            ->view('emails.shipping-quote-request');

        foreach ($this->files as $file) {
            $this->attach($file->getRealPath(), [
                'as'   => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
            ]);
        }

        return $this;
    }
}
