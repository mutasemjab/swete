@extends('layouts.app')

@section('title', __('warehouse.purchase_invoices_list'))
@section('breadcrumb', __('warehouse.purchase_invoices_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.purchase_invoices_list') }}</h1>
    </div>
    <a href="{{ route('purchase-invoices.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('warehouse.add_purchase_invoice') }}
    </a>
</div>

<div class="card overflow-hidden">
    @if($invoices->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-invoice-dollar text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.no_purchase_invoices') }}</p>
            <a href="{{ route('purchase-invoices.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('warehouse.add_purchase_invoice') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_invoice_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_invoice_supplier_invoice_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_invoice_supplier') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_invoice_warehouse') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_invoice_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_total') }}</th>
                        <th class="px-5 py-3.5 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('purchase-invoices.show', $invoice) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $invoice->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $invoice->supplier_invoice_number }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $invoice->supplier?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $invoice->warehouse?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $invoice->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-800" dir="ltr">{{ number_format($invoice->total, 3) }}</td>
                        <td class="px-5 py-4 text-end">
                            <a href="{{ route('purchase-invoices.show', $invoice) }}" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                            </a>
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
