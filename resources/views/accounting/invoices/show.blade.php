@extends('layouts.app')

@section('title', $invoice->number)
@section('breadcrumb', $invoice->number)

@section('content')
@php
    $statusStyles = ['draft' => 'bg-amber-100 text-amber-700', 'posted' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
@endphp
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            {{ $invoice->number }}
            <span class="badge {{ $statusStyles[$invoice->status] }}">{{ __('accounting.invoice_status_' . $invoice->status) }}</span>
        </h1>
        <p class="page-subtitle">{{ $invoice->invoiceType?->localized_name }}</p>
    </div>
    <a href="{{ route('accounting.invoices.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_party') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->party?->localized_name }} <span class="text-slate-400 font-normal">({{ $invoice->party?->code }})</span></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_due_date') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_currency') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->currency?->localized_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('settings.user') }}</dt>
            <dd class="font-bold text-slate-800">{{ $invoice->creator?->name }}</dd>
        </div>
        @if($invoice->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('accounting.invoice_notes') }}</dt>
            <dd class="text-slate-700">{{ $invoice->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('accounting.invoice_items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_description') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_quantity') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_total') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $item)
                <tr>
                    <td class="px-5 py-3 text-slate-700">{{ $item->description }}</td>
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
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-500">{{ __('accounting.invoice_subtotal') }}</dt>
                <dd class="font-bold text-slate-800">{{ number_format($invoice->subtotal, 3) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">{{ __('accounting.invoice_tax_total') }}</dt>
                <dd class="font-bold text-slate-800">{{ number_format($invoice->tax_total, 3) }}</dd>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-100">
                <dt class="text-slate-700 font-bold">{{ __('accounting.invoice_total') }}</dt>
                <dd class="font-black text-lg text-blue-700">{{ number_format($invoice->total, 3) }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
