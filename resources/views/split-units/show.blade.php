@extends('layouts.app')

@section('title', $splitUnit->number)
@section('breadcrumb', $splitUnit->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $splitUnit->number }}</h1>
        <p class="page-subtitle">{{ __('split_units.split_unit') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('split-units.print', $splitUnit) }}" target="_blank" class="btn-secondary">
            <i class="fa-solid fa-print"></i>
            {{ __('external_purchases.print') }}
        </a>
        <a href="{{ route('split-units.edit', $splitUnit) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('split-units.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->customer?->localized_name }} ({{ $splitUnit->customer?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('split_units.date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('external_purchases.request_branch') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->branch?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender_currency') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->currency?->localized_name }} ({{ $splitUnit->currency?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('split_units.validity_weeks') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->validity_weeks ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.quote_supply_scope') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->supplyScope?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.quote_delivery_term') }}</dt>
            <dd class="font-bold text-slate-800">{{ $splitUnit->deliveryTerm?->localized_name ?? '—' }}</dd>
        </div>
        <div class="sm:col-span-3 flex flex-wrap gap-2">
            <span class="badge {{ $splitUnit->winching_included ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ __('tenders.quote_winching_included') }}: {{ $splitUnit->winching_included ? __('app.yes') : __('app.no') }}</span>
            <span class="badge {{ $splitUnit->sales_tax_included ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ __('tenders.quote_sales_tax_included') }}: {{ $splitUnit->sales_tax_included ? __('app.yes') : __('app.no') }}</span>
            <span class="badge {{ $splitUnit->customs_fees_included ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ __('tenders.quote_customs_fees_included') }}: {{ $splitUnit->customs_fees_included ? __('app.yes') : __('app.no') }}</span>
        </div>
        @if($splitUnit->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_notes') }}</dt>
            <dd class="text-slate-700">{{ $splitUnit->notes }}</dd>
        </div>
        @endif
        @if($splitUnit->additional_terms)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.quote_additional_terms') }}</dt>
            <dd class="text-slate-700 whitespace-pre-line">{{ $splitUnit->additional_terms }}</dd>
        </div>
        @endif
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('split_units.assigned_to') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($splitUnit->invoice_id)
                    <span class="badge bg-emerald-100 text-emerald-700">{{ __('split_units.status_invoiced') }}</span>
                @elseif($splitUnit->assignee)
                    {{ $splitUnit->assignee->name }}
                    <span class="badge bg-violet-100 text-violet-700 ms-1">{{ __('split_units.status_pending_invoice') }}</span>
                @else
                    <span class="text-slate-400 font-normal">{{ __('split_units.unassigned') }}</span>
                @endif
            </dd>
        </div>
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('split_units.items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_total') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('split_units.item_notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($splitUnit->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->unit_price, 3) }}</td>
                    <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($item->total, 3) }}</td>
                    <td class="px-5 py-3 text-xs text-slate-600">
                        @forelse($item->notes ?? [] as $note)
                            <p>• {{ $note }}</p>
                        @empty
                            —
                        @endforelse
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-80">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium">{{ __('tenders.quote_subtotal') }}</dt>
                <dd class="font-bold text-slate-800">{{ number_format($splitUnit->subtotal, 3) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium">
                    {{ __('tenders.quote_discount') }}
                    @if($splitUnit->discount_type === 'percent' && $splitUnit->discount_amount > 0)
                        <span class="text-xs text-slate-400" dir="ltr">({{ rtrim(rtrim(number_format($splitUnit->discount_value, 3), '0'), '.') }}%)</span>
                    @endif
                </dt>
                <dd class="font-bold text-rose-600">- {{ number_format($splitUnit->discount_amount, 3) }}</dd>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <dt class="text-slate-700 font-bold">{{ __('tenders.quote_total') }}</dt>
                <dd class="font-black text-lg text-sky-700">{{ number_format($splitUnit->total, 3) }} {{ $splitUnit->currency?->code }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
