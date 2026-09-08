<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\PurchaseRequestApprover;
use App\Models\User;
use Illuminate\Http\Request;

class PurchaseRequestApproverController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $users             = User::where('status', true)->orderBy('name')->get();
        $currentApproverIds = PurchaseRequestApprover::pluck('user_id');

        return $this->moduleView('settings.purchase-request-approvers.index', compact('users', 'currentApproverIds'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'approver_ids'   => ['array'],
            'approver_ids.*' => ['exists:users,id'],
        ]);

        PurchaseRequestApprover::query()->delete();

        foreach ($validated['approver_ids'] ?? [] as $userId) {
            PurchaseRequestApprover::create(['user_id' => $userId]);
        }

        return redirect()->route('settings.purchase-request-approvers.index')
            ->with('success', __('settings.purchase_request_approvers_saved'));
    }
}
