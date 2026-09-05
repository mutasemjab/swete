@extends('layouts.app')

@section('title', __('accounting.invoices_list'))
@section('breadcrumb', __('accounting.invoices'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.invoices_list') }}</h1>
        <p class="page-subtitle">{{ __('accounting.invoices_subtitle') }}</p>
    </div>
    <a href="{{ route('accounting.invoices.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('accounting.add_invoice') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $invoices->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('accounting.total_invoices') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('accounting.invoices.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="invoice_type_id" class="form-select w-52">
        <option value="">{{ __('accounting.all_invoice_types') }}</option>
        @foreach($invoiceTypes as $invoiceType)
            <option value="{{ $invoiceType->id }}" @selected(request('invoice_type_id') == $invoiceType->id)>{{ $invoiceType->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['invoice_type_id']))
        <a href="{{ route('accounting.invoices.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($invoices->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-invoice-dollar text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('accounting.no_invoices') }}</p>
            <a href="{{ route('accounting.invoices.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('accounting.add_first_invoice') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_type') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_party') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_total') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $statusStyles = ['draft' => 'bg-amber-100 text-amber-700', 'posted' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
                    @endphp
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('accounting.invoices.show', $invoice) }}"
                               class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $invoice->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $invoice->invoiceType?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $invoice->party?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $invoice->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 font-bold text-slate-800">{{ number_format($invoice->total, 3) }}</td>
                        <td class="px-5 py-4">
                            <span class="badge {{ $statusStyles[$invoice->status] }}">{{ __('accounting.invoice_status_' . $invoice->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $invoices->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $invoices->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $invoices->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($invoices->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $invoices->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($invoices->hasMorePages())
                    <a href="{{ $invoices->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
