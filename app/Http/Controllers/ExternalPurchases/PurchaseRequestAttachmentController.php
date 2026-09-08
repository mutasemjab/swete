<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestAttachment;
use Illuminate\Http\Request;

class PurchaseRequestAttachmentController extends Controller
{
    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'url'   => ['required', 'string', 'max:500'],
            'label' => ['nullable', 'string', 'max:150'],
        ]);

        $purchaseRequest->attachments()->create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', __('external_purchases.attachment_added'));
    }

    public function destroy(PurchaseRequest $purchaseRequest, PurchaseRequestAttachment $attachment)
    {
        abort_unless($attachment->purchase_request_id === $purchaseRequest->id, 404);

        $attachment->delete();

        return back()->with('success', __('external_purchases.attachment_deleted'));
    }
}
