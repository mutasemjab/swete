<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ApprovalService
{
    public function request(Model $approvable, User $approver, string $action = 'default', ?string $note = null): Approval
    {
        $approval = $approvable->approvals()->create([
            'action'       => $action,
            'status'       => 'pending',
            'requested_by' => auth()->id(),
            'approver_id'  => $approver->id,
            'note'         => $note,
        ]);

        activity()
            ->performedOn($approvable)
            ->causedBy(auth()->user())
            ->withProperties(['approval_id' => $approval->id, 'approver' => $approver->name, 'action' => $action])
            ->log('approval_requested');

        return $approval;
    }

    public function approve(Approval $approval, User $decider, ?string $note = null): Approval
    {
        $approval->update([
            'status'        => 'approved',
            'decision_note' => $note,
            'decided_at'    => now(),
        ]);

        $approvable = $approval->approvable;

        if ($approvable && method_exists($approvable, 'onApproved')) {
            $approvable->onApproved($approval);
        }

        activity()
            ->performedOn($approvable ?? $approval)
            ->causedBy($decider)
            ->withProperties(['approval_id' => $approval->id])
            ->log('approval_approved');

        return $approval;
    }

    public function reject(Approval $approval, User $decider, ?string $note = null): Approval
    {
        $approval->update([
            'status'        => 'rejected',
            'decision_note' => $note,
            'decided_at'    => now(),
        ]);

        $approvable = $approval->approvable;

        if ($approvable && method_exists($approvable, 'onRejected')) {
            $approvable->onRejected($approval);
        }

        activity()
            ->performedOn($approvable ?? $approval)
            ->causedBy($decider)
            ->withProperties(['approval_id' => $approval->id])
            ->log('approval_rejected');

        return $approval;
    }
}
