<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public function request(Model $approvable, User $approver, string $action = 'default', ?string $note = null): Approval
    {
        return $this->requestToMany($approvable, [$approver], $action, $note)->first();
    }

    /**
     * Request approval from a pool of eligible approvers — any ONE of them deciding
     * is enough (OR logic). Creates one Approval row per approver; approve()/reject()
     * cancel the remaining siblings once the first decision lands.
     */
    public function requestToMany(Model $approvable, iterable $approvers, string $action = 'default', ?string $note = null): Collection
    {
        $approvals = collect($approvers)->map(fn (User $approver) => $approvable->approvals()->create([
            'action'       => $action,
            'status'       => 'pending',
            'requested_by' => auth()->id(),
            'approver_id'  => $approver->id,
            'note'         => $note,
        ]));

        activity()
            ->performedOn($approvable)
            ->causedBy(auth()->user())
            ->withProperties(['approvers' => collect($approvers)->pluck('name'), 'action' => $action])
            ->log('approval_requested');

        return $approvals;
    }

    public function approve(Approval $approval, User $decider, ?string $note = null): Approval
    {
        return DB::transaction(function () use ($approval, $decider, $note) {
            $approval = Approval::whereKey($approval->id)->lockForUpdate()->firstOrFail();

            if (! $approval->isPending()) {
                return $approval;
            }

            $approval->update([
                'status'        => 'approved',
                'decision_note' => $note,
                'decided_at'    => now(),
            ]);

            $this->cancelSiblings($approval);

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
        });
    }

    public function reject(Approval $approval, User $decider, ?string $note = null): Approval
    {
        return DB::transaction(function () use ($approval, $decider, $note) {
            $approval = Approval::whereKey($approval->id)->lockForUpdate()->firstOrFail();

            if (! $approval->isPending()) {
                return $approval;
            }

            $approval->update([
                'status'        => 'rejected',
                'decision_note' => $note,
                'decided_at'    => now(),
            ]);

            $this->cancelSiblings($approval);

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
        });
    }

    /** Once one of several eligible approvers decides, the rest no longer need to. */
    private function cancelSiblings(Approval $approval): void
    {
        Approval::where('approvable_type', $approval->approvable_type)
            ->where('approvable_id', $approval->approvable_id)
            ->where('action', $approval->action)
            ->where('id', '!=', $approval->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'decided_at' => now()]);
    }
}
