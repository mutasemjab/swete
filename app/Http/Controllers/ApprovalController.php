<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\PurchaseRequestApproval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'for_me');

        $pendingForMe = Approval::with(['approvable', 'requester'])
            ->where('approver_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Purchase requests use their own unanimous-approval mechanism (not the
        // generic Approval model — see PurchaseRequest::seedApprovals()), so they
        // need to be surfaced here separately for the approver to actually find them.
        $pendingPurchaseRequestApprovals = PurchaseRequestApproval::with(['purchaseRequest.creator'])
            ->where('user_id', auth()->id())
            ->where('decision', 'pending')
            ->latest()
            ->get();

        // A rule-triggered request can fan out into one Approval row per eligible
        // approver (OR logic) — collapse those back into a single summarizing row
        // per logical request, preferring a real decision over a cancelled sibling.
        $statusPriority = ['approved' => 0, 'rejected' => 1, 'pending' => 2, 'cancelled' => 3];

        $submittedByMe = Approval::with(['approvable', 'approver'])
            ->where('requested_by', auth()->id())
            ->get()
            ->groupBy(fn ($a) => "{$a->approvable_type}#{$a->approvable_id}#{$a->action}")
            ->map(fn ($group) => $group->sortBy(fn ($a) => $statusPriority[$a->status] ?? 9)->first())
            ->sortByDesc('created_at')
            ->values();

        $currentModule       = 'approvals';
        $currentModuleConfig = [
            'name'     => 'approvals.my_approvals',
            'icon'     => 'clipboard-check',
            'color'    => 'indigo',
            'route'    => 'approvals.index',
            'gradient' => 'from-indigo-500 to-indigo-700',
            'sections' => [],
        ];

        return view('approvals.index', compact('tab', 'pendingForMe', 'pendingPurchaseRequestApprovals', 'submittedByMe', 'currentModule', 'currentModuleConfig'));
    }

    public function approve(Request $request, Approval $approval, ApprovalService $approvals)
    {
        abort_unless($approval->approver_id === auth()->id(), 403);

        $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        if ($approval->isPending()) {
            $approvals->approve($approval, auth()->user(), $request->input('note'));
        }

        return back()->with('success', __('approvals.approved'));
    }

    public function reject(Request $request, Approval $approval, ApprovalService $approvals)
    {
        abort_unless($approval->approver_id === auth()->id(), 403);

        $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        if ($approval->isPending()) {
            $approvals->reject($approval, auth()->user(), $request->input('note'));
        }

        return back()->with('success', __('approvals.rejected'));
    }
}
