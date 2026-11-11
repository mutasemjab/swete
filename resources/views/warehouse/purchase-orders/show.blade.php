@extends('layouts.app')

@section('title', $order->number)
@section('breadcrumb', $order->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $order->number }}
            @if($order->status === 'draft')
                <span class="badge bg-amber-100 text-amber-700">{{ __('warehouse.purchase_order_status_draft') }}</span>
            @else
                <span class="badge bg-emerald-100 text-emerald-700">{{ __('warehouse.purchase_order_status_converted') }}</span>
            @endif
        </h1>
    </div>
    <div class="flex items-center gap-2">
        @if($order->isDraft())
            <a href="{{ route('purchase-orders.edit', $order) }}" class="btn-secondary">
                <i class="fa-solid fa-pen"></i> {{ __('app.edit') }}
            </a>
            <a href="{{ route('purchase-invoices.create', ['purchase_order_id' => $order->id]) }}" class="btn-primary">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                {{ __('warehouse.purchase_order_convert') }}
            </a>
        @else
            <a href="{{ route('purchase-invoices.show', $order->invoice) }}" class="btn-primary">
                <i class="fa-solid fa-eye"></i>
                {{ __('warehouse.purchase_order_view_invoice') }}
            </a>
        @endif
        @include('components.back-link', ['url' => $backUrl])
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_order_supplier') }}</dt>
            <dd class="font-bold text-slate-800">{{ $order->supplier?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_order_warehouse') }}</dt>
            <dd class="font-bold text-slate-800">{{ $order->warehouse?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_order_date') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $order->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_created_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $order->creator?->name }}</dd>
        </div>
        @if($order->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_order_notes') }}</dt>
            <dd class="text-slate-700">{{ $order->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('warehouse.purchase_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_total') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700" dir="ltr">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-700" dir="ltr">{{ number_format($item->unit_price, 4) }}</td>
                    <td class="px-5 py-3 font-bold text-slate-800" dir="ltr">{{ number_format($item->total, 3) }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $item->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-50/50">
                    <td colspan="3" class="px-5 py-3 text-end font-bold text-slate-600">{{ __('warehouse.purchase_total') }}</td>
                    <td class="px-5 py-3 font-black text-slate-800" dir="ltr">{{ number_format($order->total, 3) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
