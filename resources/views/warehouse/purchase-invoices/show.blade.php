@extends('layouts.app')

@section('title', $invoice->number)
@section('breadcrumb', $invoice->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $invoice->number }}</h1>
        <p class="page-subtitle" dir="ltr">{{ $invoice->supplier_invoice_number }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_supplier') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->supplier?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_supplier_invoice_number') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $invoice->supplier_invoice_number }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_warehouse') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->warehouse?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_date') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ $invoice->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.voucher_created_by') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->creator?->name }}</dd>
        </div>
        @if($invoice->purchaseOrder)
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_from_order') }}</dt>
            <dd class="font-bold text-slate-800">
                <a href="{{ route('purchase-orders.show', $invoice->purchaseOrder) }}" class="text-indigo-600 hover:underline">{{ $invoice->purchaseOrder->number }}</a>
            </dd>
        </div>
        @endif
        @if($invoice->stockVoucher)
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_voucher_link') }}</dt>
            <dd class="font-bold text-slate-800">
                <a href="{{ route('warehouse.vouchers.show', ['type' => 'receipt', 'voucher' => $invoice->stockVoucher]) }}" class="text-indigo-600 hover:underline">{{ $invoice->stockVoucher->number }}</a>
            </dd>
        </div>
        @endif
        @if($invoice->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.purchase_invoice_notes') }}</dt>
            <dd class="text-slate-700">{{ $invoice->notes }}</dd>
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
                @foreach($invoice->items as $item)
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
                    <td class="px-5 py-3 font-black text-slate-800" dir="ltr">{{ number_format($invoice->total, 3) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
