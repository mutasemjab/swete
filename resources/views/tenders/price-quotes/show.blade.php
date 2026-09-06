@extends('layouts.app')

@section('title', $priceQuote->number)
@section('breadcrumb', $priceQuote->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $priceQuote->number }}</h1>
        <p class="page-subtitle">{{ __('tenders.price_quote') }}</p>
    </div>
    <a href="{{ route('price-quotes.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.customer') }}</dt>
            <dd class="font-bold text-slate-800">{{ $priceQuote->customer?->localized_name }} ({{ $priceQuote->customer?->code }})</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.quote_tender') }}</dt>
            <dd class="font-bold text-slate-800">
                @if($priceQuote->tender)
                    <a href="{{ route('tenders.show', $priceQuote->tender) }}" class="text-indigo-600 hover:underline">{{ $priceQuote->tender->number }}</a>
                @else
                    —
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.quote_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $priceQuote->date->format('Y-m-d') }}</dd>
        </div>
        @if($priceQuote->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_notes') }}</dt>
            <dd class="text-slate-700">{{ $priceQuote->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.quote_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_total') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($priceQuote->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800">{{ $item->material?->localized_name }}</span>
                        <span class="text-xs text-slate-400 ms-1">{{ $item->material?->unit?->symbol }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-5 py-3 text-slate-700">{{ number_format($item->unit_price, 3) }}</td>
                    <td class="px-5 py-3 font-bold text-slate-800">{{ number_format($item->total, 3) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-80">
        <div class="flex justify-between pt-2">
            <dt class="text-slate-700 font-bold">{{ __('tenders.quote_total') }}</dt>
            <dd class="font-black text-lg text-orange-700">{{ number_format($priceQuote->total, 3) }}</dd>
        </div>
    </div>
</div>
@endsection
