<?php

namespace App\Http\Controllers;

use App\Models\Approval;
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

        $submittedByMe = Approval::with(['approvable', 'approver'])
            ->where('requested_by', auth()->id())
            ->latest()
            ->get();

        $currentModule       = 'approvals';
        $currentModuleConfig = [
            'name'     => 'approvals.my_approvals',
            'icon'     => 'clipboard-check',
            'color'    => 'indigo',
            'route'    => 'approvals.index',
            'gradient' => 'from-indigo-500 to-indigo-700',
            'sections' => [],
        ];

        return view('approvals.index', compact('tab', 'pendingForMe', 'submittedByMe', 'currentModule', 'currentModuleConfig'));
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
