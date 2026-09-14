<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\PurchaseRequestReminderRecipient;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseRequestReminderRecipientController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $users              = User::where('status', true)->orderBy('name')->get();
        $currentRecipientIds = PurchaseRequestReminderRecipient::pluck('user_id');

        return $this->moduleView('settings.purchase-request-reminder-recipients.index', compact('users', 'currentRecipientIds'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'recipient_ids'   => ['array'],
            'recipient_ids.*' => ['exists:users,id'],
        ]);

        PurchaseRequestReminderRecipient::query()->delete();

        foreach ($validated['recipient_ids'] ?? [] as $userId) {
            PurchaseRequestReminderRecipient::create(['user_id' => $userId]);
        }

        return redirect()->route('settings.purchase-request-reminder-recipients.index')
            ->with('success', __('settings.purchase_request_reminder_recipients_saved'));
    }
}
