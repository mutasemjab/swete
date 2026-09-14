@extends('layouts.app')

@section('title', __('tenders.reminder'))
@section('breadcrumb', __('tenders.reminder'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ __('tenders.reminder') }}
            @if($reminder->status === 'fulfilled')
                <span class="badge bg-emerald-100 text-emerald-700">{{ __('tenders.reminder_status_fulfilled') }}</span>
            @else
                <span class="badge bg-amber-100 text-amber-700">{{ __('tenders.reminder_status_pending') }}</span>
            @endif
        </h1>
        <p class="page-subtitle">{{ $reminder->project?->number }} — {{ $reminder->project?->localized_title }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($reminder->status === 'pending')
            <a href="{{ route('purchase-requests.create', ['reminder_id' => $reminder->id]) }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i>
                {{ __('tenders.reminder_create_pr') }}
            </a>
        @else
            <a href="{{ route('purchase-requests.show', $reminder->purchase_request_id) }}" class="btn-primary">
                <i class="fa-solid fa-eye"></i>
                {{ __('tenders.reminder_view_pr') }}
            </a>
        @endif
        <a href="{{ route('purchase-request-reminders.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project') }}</dt>
            <dd class="font-bold text-slate-800">
                <a href="{{ route('projects.show', $reminder->project) }}" class="text-indigo-600 hover:underline">{{ $reminder->project?->number }}</a>
                — {{ $reminder->project?->localized_title }}
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project_customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $reminder->project?->customer?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('approvals.requested_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $reminder->requester?->name }} — {{ $reminder->created_at->format('Y-m-d') }}</dd>
        </div>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.reminder_drive_url') }}</dt>
            <dd class="font-bold text-slate-800">
                <a href="{{ $reminder->google_drive_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all">
                    <i class="fa-brands fa-google-drive"></i> {{ $reminder->google_drive_url }}
                </a>
            </dd>
        </div>
        @if($reminder->status === 'fulfilled')
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.reminder_fulfilled_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $reminder->fulfiller?->name }} — {{ $reminder->fulfilled_at?->format('Y-m-d H:i') }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.reminder_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($reminder->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
