@extends('layouts.app')

@section('title', $materialRequest->number)
@section('breadcrumb', $materialRequest->number)

@section('content')
@php
    $statusStyles = [
        'draft'            => 'bg-slate-100 text-slate-500',
        'pending_approval' => 'bg-amber-100 text-amber-700',
        'approved'         => 'bg-blue-100 text-blue-700',
        'rejected'         => 'bg-rose-100 text-rose-700',
        'fulfilled'        => 'bg-emerald-100 text-emerald-700',
        'cancelled'        => 'bg-slate-100 text-slate-500',
    ];
@endphp
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $materialRequest->number }}
            <span class="badge {{ $statusStyles[$materialRequest->status] }}">{{ __('warehouse.material_request_status_' . $materialRequest->status) }}</span>
        </h1>
        <p class="page-subtitle">{{ __('warehouse.material_request') }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($materialRequest->status === 'approved')
        <form action="{{ route('warehouse.material-requests.fulfill', $materialRequest) }}" method="POST"
              @submit="if (! confirm('{{ __('warehouse.material_request_fulfill_confirm') }}')) $event.preventDefault()">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-box-open"></i>
                {{ __('warehouse.material_request_fulfill') }}
            </button>
        </form>
        @endif
        <a href="{{ route('warehouse.material-requests.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-4">{{ __('warehouse.material_request') }}</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_request_warehouse') }}</dt>
                <dd class="font-bold text-slate-800">{{ $materialRequest->warehouse?->localized_name }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_request_requester') }}</dt>
                <dd class="font-bold text-slate-800">{{ $materialRequest->requester?->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_request_needed_by') }}</dt>
                <dd class="font-bold text-slate-800">{{ $materialRequest->needed_by_date?->format('Y-m-d') ?? '—' }}</dd>
            </div>
            @if($materialRequest->fulfilledVoucher)
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_request_fulfilled_voucher') }}</dt>
                <dd>
                    <a href="{{ route('warehouse.vouchers.show', ['type' => 'issue', 'voucher' => $materialRequest->fulfilledVoucher]) }}"
                       class="font-mono font-bold text-indigo-600 hover:underline">{{ $materialRequest->fulfilledVoucher->number }}</a>
                </dd>
            </div>
            @endif
            @if($materialRequest->reason)
            <div class="sm:col-span-2">
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_request_reason') }}</dt>
                <dd class="text-slate-700">{{ $materialRequest->reason }}</dd>
            </div>
            @endif
        </dl>
    </div>

    @include('components.approval-actions', [
        'model' => $materialRequest,
        'requestRoute' => route('warehouse.material-requests.request-approval', $materialRequest),
        'approvers' => $approvers,
    ])
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('warehouse.voucher_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($materialRequest->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $item->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
