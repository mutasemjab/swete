{{--
    Reusable "approval" widget for any module's show/index page.

    Props:
    - $model         the approvable instance (uses HasApprovals)
    - $requestRoute  named route the "send for approval" form posts to (module-specific)
    - $approvers     collection of users to choose from as the target approver
--}}
@php
    $latest = $model->latestApproval();
@endphp

<div class="card px-5 py-4">
    <h3 class="text-sm font-black text-slate-700 mb-3">{{ __('approvals.approvals') }}</h3>

    @if($latest)
        <div class="flex items-center gap-3 flex-wrap">
            @include('components.approval-badge', ['status' => $latest->status])
            <span class="text-sm text-slate-500">
                {{ __('approvals.pending_approval_from') }}
                <span class="font-bold text-slate-700">{{ $latest->approver?->name }}</span>
            </span>
        </div>

        @if($latest->isPending() && $latest->approver_id === auth()->id())
            <div class="flex items-center gap-2 mt-4">
                <form action="{{ route('approvals.approve', $latest) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm">
                        <i class="fa-solid fa-check"></i> {{ __('approvals.approve') }}
                    </button>
                </form>
                <form action="{{ route('approvals.reject', $latest) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger btn-sm">
                        <i class="fa-solid fa-xmark"></i> {{ __('approvals.reject') }}
                    </button>
                </form>
            </div>
        @endif
    @elseif(isset($requestRoute) && isset($approvers))
        <form action="{{ $requestRoute }}" method="POST" class="flex items-end gap-3 flex-wrap">
            @csrf
            <div class="min-w-48">
                <label class="form-label">{{ __('approvals.choose_approver') }}</label>
                <select name="approver_id" class="form-select" required>
                    @foreach($approvers as $approver)
                        <option value="{{ $approver->id }}">{{ $approver->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-48">
                <label class="form-label">{{ __('approvals.approval_note') }}</label>
                <input type="text" name="note" class="form-input">
            </div>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-paper-plane"></i> {{ __('approvals.send_for_approval') }}
            </button>
        </form>
    @else
        <p class="text-sm text-slate-400">—</p>
    @endif
</div>
