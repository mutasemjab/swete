@extends('layouts.app')

@section('title', __('approvals.my_approvals'))
@section('breadcrumb', __('approvals.my_approvals'))

@section('content')
<div x-data="{ tab: '{{ $tab === 'by_me' ? 'by_me' : 'for_me' }}' }">

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('approvals.my_approvals') }}</h1>
            <p class="page-subtitle">{{ __('approvals.my_approvals_subtitle') }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6">
        <button type="button" @click="tab = 'for_me'"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all"
                :class="tab === 'for_me' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            {{ __('approvals.tab_for_me') }}
            @if($pendingForMe->count())
                <span class="ms-1.5 px-1.5 py-0.5 rounded-md text-[11px]" :class="tab === 'for_me' ? 'bg-white/20' : 'bg-rose-100 text-rose-600'">{{ $pendingForMe->count() }}</span>
            @endif
        </button>
        <button type="button" @click="tab = 'by_me'"
                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all"
                :class="tab === 'by_me' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            {{ __('approvals.tab_by_me') }}
        </button>
    </div>

    {{-- Pending for me --}}
    <div x-show="tab === 'for_me'" class="card overflow-hidden">
        @if($pendingForMe->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                    <i class="fa-solid fa-clipboard-check text-slate-400 text-3xl"></i>
                </div>
                <p class="text-slate-800 font-bold text-lg">{{ __('approvals.no_pending_for_me') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.item') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_by') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('approvals.note') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_at') }}</th>
                            <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingForMe as $approval)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 font-bold text-slate-800">{{ $approval->approvable_label }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $approval->requester?->name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-500 hidden md:table-cell">{{ $approval->note ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-500">{{ $approval->created_at->diffForHumans() }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('approvals.approve', $approval) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-primary btn-sm">
                                            <i class="fa-solid fa-check"></i> {{ __('approvals.approve') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('approvals.reject', $approval) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-danger btn-sm">
                                            <i class="fa-solid fa-xmark"></i> {{ __('approvals.reject') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Submitted by me --}}
    <div x-show="tab === 'by_me'" class="card overflow-hidden">
        @if($submittedByMe->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                    <i class="fa-solid fa-paper-plane text-slate-400 text-3xl"></i>
                </div>
                <p class="text-slate-800 font-bold text-lg">{{ __('approvals.no_submitted_by_me') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.item') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_to') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('approvals.decision_note') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('approvals.requested_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submittedByMe as $approval)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 font-bold text-slate-800">{{ $approval->approvable_label }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $approval->approver?->name }}</td>
                            <td class="px-5 py-4">
                                @include('components.approval-badge', ['status' => $approval->status])
                                @if($approval->approvable instanceof \App\Models\PendingAction && $approval->approvable->status === 'failed')
                                    <span class="badge bg-rose-100 text-rose-700 ms-1" title="{{ $approval->approvable->error_message }}">
                                        <i class="fa-solid fa-triangle-exclamation"></i> {{ __('approvals.action_failed') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500 hidden md:table-cell">{{ $approval->decision_note ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-500">{{ $approval->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
