<?php

namespace App\Models\Concerns;

use App\Models\Approval;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Lets a model be routed through the generic approval workflow.
 *
 * The model may optionally implement onApproved()/onRejected() hooks —
 * ApprovalService calls them (if present) right after flipping the
 * approval's status, so each model can react in its own terms (e.g.
 * MaterialRequest::onApproved() sets its own status to 'approved')
 * without ApprovalService needing to know module-specific rules.
 */
trait HasApprovals
{
    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function latestApproval(): ?Approval
    {
        return $this->approvals()->latest()->first();
    }

    public function isPendingApproval(): bool
    {
        return $this->latestApproval()?->isPending() ?? false;
    }

    public function requestApproval(User $approver, string $action = 'default', ?string $note = null): Approval
    {
        return app(ApprovalService::class)->request($this, $approver, $action, $note);
    }
}
