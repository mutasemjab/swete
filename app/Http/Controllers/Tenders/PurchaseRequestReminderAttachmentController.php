<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\PurchaseRequestReminder;
use App\Models\PurchaseRequestReminderAttachment;
use App\Support\FileStorage;
use Illuminate\Http\Request;

class PurchaseRequestReminderAttachmentController extends ModuleController
{
    protected string $module = 'tenders';

    /** Accepts one or more {file} rows in a single submit — mirrors ProjectAttachmentController::store(). */
    public function store(Request $request, PurchaseRequestReminder $purchaseRequestReminder)
    {
        abort_unless($purchaseRequestReminder->isEditable(), 403, __('tenders.reminder_locked'));

        $validated = $request->validate([
            'attachments'        => ['nullable', 'array'],
            'attachments.*.file' => ['nullable', 'file', 'max:20480'],
        ]);

        foreach ($validated['attachments'] ?? [] as $row) {
            if (empty($row['file'])) {
                continue;
            }

            $purchaseRequestReminder->attachments()->create([
                'path'          => FileStorage::store($row['file'], 'purchase-request-reminders'),
                'original_name' => $row['file']->getClientOriginalName(),
                'created_by'    => $request->user()->id,
            ]);
        }

        return back()->with('success', __('tenders.reminder_attachment_added'));
    }

    public function destroy(PurchaseRequestReminder $purchaseRequestReminder, PurchaseRequestReminderAttachment $attachment)
    {
        abort_unless($attachment->purchase_request_reminder_id === $purchaseRequestReminder->id, 404);
        abort_unless($purchaseRequestReminder->isEditable(), 403, __('tenders.reminder_locked'));

        FileStorage::delete($attachment->path);
        $attachment->delete();

        return back()->with('success', __('tenders.reminder_attachment_deleted'));
    }
}
