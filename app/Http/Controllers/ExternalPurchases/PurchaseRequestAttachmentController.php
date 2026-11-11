<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestAttachment;
use App\Support\FileStorage;
use Illuminate\Http\Request;

class PurchaseRequestAttachmentController extends Controller
{
    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'file'  => ['required', 'file', 'max:20480'],
            'label' => ['nullable', 'string', 'max:150'],
        ]);

        $purchaseRequest->attachments()->create([
            'path'          => FileStorage::store($validated['file'], 'purchase-requests'),
            'original_name' => $validated['file']->getClientOriginalName(),
            'label'         => $validated['label'] ?? null,
            'created_by'    => $request->user()->id,
        ]);

        return back()->with('success', __('external_purchases.attachment_added'));
    }

    public function destroy(PurchaseRequest $purchaseRequest, PurchaseRequestAttachment $attachment)
    {
        abort_unless($attachment->purchase_request_id === $purchaseRequest->id, 404);

        FileStorage::delete($attachment->path);
        $attachment->delete();

        return back()->with('success', __('external_purchases.attachment_deleted'));
    }
}
