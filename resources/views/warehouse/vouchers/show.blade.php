@extends('layouts.app')

@section('title', $voucher->number)
@section('breadcrumb', $voucher->number)

@section('content')
@php
    $statusStyles = ['draft' => 'bg-amber-100 text-amber-700', 'posted' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
@endphp
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $voucher->number }}
            <span class="badge {{ $statusStyles[$voucher->status] }}">{{ __('warehouse.voucher_status_' . $voucher->status) }}</span>
        </h1>
        <p class="page-subtitle">{{ __('warehouse.voucher_type_' . $type) }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($voucher->isDraft())
        <form action="{{ route('warehouse.vouchers.post', ['type' => $type, 'voucher' => $voucher]) }}" method="POST"
              @submit="if (! confirm('{{ __('warehouse.voucher_post_confirm') }}')) $event.preventDefault()">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-check"></i>
                {{ __('warehouse.voucher_post') }}
            </button>
        </form>
        @endif
        <a href="{{ route('warehouse.vouchers.index', ['type' => $type]) }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">
                {{ $type === 'transfer' ? __('warehouse.voucher_source_warehouse') : __('warehouse.voucher_warehouse') }}
            </dt>
            <dd class="font-bold text-slate-800">{{ $voucher->warehouse?->localized_name }}</dd>
        </div>
        @if($type === 'transfer')
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_destination_warehouse') }}</dt>
            <dd class="font-bold text-slate-800">{{ $voucher->destinationWarehouse?->localized_name }}</dd>
        </div>
        @endif
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $voucher->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_reference_no') }}</dt>
            <dd class="font-bold text-slate-800">{{ $voucher->reference_no ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_created_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $voucher->creator?->name }}</dd>
        </div>
        @if($voucher->posted_by)
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_posted_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $voucher->poster?->name }} — {{ $voucher->posted_at?->format('Y-m-d H:i') }}</dd>
        </div>
        @endif
        @if($voucher->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_notes') }}</dt>
            <dd class="text-slate-700">{{ $voucher->notes }}</dd>
        </div>
        @endif
    </dl>
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
                    @if($type === 'receipt')
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_unit_cost') }}</th>
                    @endif
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($voucher->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                    @if($type === 'receipt')
                    <td class="px-5 py-3 text-slate-700">{{ $item->unit_cost !== null ? number_format($item->unit_cost, 4) : '—' }}</td>
                    @endif
                    <td class="px-5 py-3 text-slate-500">{{ $item->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
