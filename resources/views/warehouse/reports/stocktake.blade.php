@extends('layouts.app')

@section('title', __('warehouse.report_stocktake'))
@section('breadcrumb', __('warehouse.report_stocktake'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.report_stocktake') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.report_stocktake_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('warehouse.reports.stocktake') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="warehouse_id" class="form-select w-52">
        <option value="">{{ __('warehouse.report_all_warehouses') }}</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>{{ $warehouse->localized_name }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-40">
    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-40">
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('warehouse.report_filter') }}
    </button>
    @if(request()->hasAny(['warehouse_id','date_from','date_to']))
        <a href="{{ route('warehouse.reports.stocktake') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($movements->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-clipboard-check text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.report_no_data') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.warehouse') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_number') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($movements as $movement)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $movement->moved_at->format('Y-m-d H:i') }}</td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800">{{ $movement->material?->localized_name }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $movement->warehouse?->localized_name }}</td>
                        <td class="px-5 py-4 font-bold text-emerald-700">
                            +{{ number_format($movement->quantity, 3) }}
                            <span class="text-xs text-slate-400 font-normal">{{ $movement->material?->unit?->symbol }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('warehouse.vouchers.show', ['type' => 'receipt', 'voucher' => $movement->stock_voucher_id]) }}"
                               class="font-mono text-sm text-indigo-600 hover:underline">{{ $movement->stockVoucher?->number }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $movements->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $movements->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $movements->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($movements->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $movements->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($movements->hasMorePages())
                    <a href="{{ $movements->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
