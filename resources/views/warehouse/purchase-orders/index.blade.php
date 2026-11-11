@extends('layouts.app')

@section('title', __('warehouse.purchase_orders_list'))
@section('breadcrumb', __('warehouse.purchase_orders_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.purchase_orders_list') }}</h1>
    </div>
    <a href="{{ route('purchase-orders.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('warehouse.add_purchase_order') }}
    </a>
</div>

<div class="card overflow-hidden">
    @if($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-signature text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.no_purchase_orders') }}</p>
            <a href="{{ route('purchase-orders.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('warehouse.add_purchase_order') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_order_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_order_supplier') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_order_warehouse') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_order_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_total') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('purchase-orders.show', $order) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $order->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->supplier?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->warehouse?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $order->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-800" dir="ltr">{{ number_format($order->total, 3) }}</td>
                        <td class="px-5 py-4">
                            @if($order->status === 'draft')
                                <span class="badge bg-amber-100 text-amber-700">{{ __('warehouse.purchase_order_status_draft') }}</span>
                            @else
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('warehouse.purchase_order_status_converted') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-end">
                            <a href="{{ route('purchase-orders.show', $order) }}" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $orders->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $orders->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $orders->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($orders->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
