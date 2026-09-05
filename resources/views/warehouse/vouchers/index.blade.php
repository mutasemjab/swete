@extends('layouts.app')

@section('title', __('warehouse.vouchers_list_' . $type))
@section('breadcrumb', __('warehouse.vouchers_list_' . $type))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.vouchers_list_' . $type) }}</h1>
        <p class="page-subtitle">{{ __('warehouse.voucher_type_' . $type) }}</p>
    </div>
    <a href="{{ route('warehouse.vouchers.create', ['type' => $type]) }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('warehouse.add_voucher_' . $type) }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-{{ $type === 'receipt' ? 'box-open' : ($type === 'issue' ? 'arrow-up-from-bracket' : 'right-left') }} text-emerald-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $vouchers->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('warehouse.total_vouchers') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    @if($vouchers->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-lines text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.no_vouchers') }}</p>
            <a href="{{ route('warehouse.vouchers.create', ['type' => $type]) }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('warehouse.add_first_voucher') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">
                            {{ $type === 'transfer' ? __('warehouse.voucher_source_warehouse') : __('warehouse.voucher_warehouse') }}
                        </th>
                        @if($type === 'transfer')
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_destination_warehouse') }}</th>
                        @endif
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($vouchers as $voucher)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('warehouse.vouchers.show', ['type' => $type, 'voucher' => $voucher]) }}"
                               class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $voucher->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $voucher->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $voucher->warehouse?->localized_name }}</td>
                        @if($type === 'transfer')
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $voucher->destinationWarehouse?->localized_name }}</td>
                        @endif
                        <td class="px-5 py-4">
                            @php
                                $statusStyles = ['draft' => 'bg-amber-100 text-amber-700', 'posted' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
                            @endphp
                            <span class="badge {{ $statusStyles[$voucher->status] }}">{{ __('warehouse.voucher_status_' . $voucher->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('warehouse.vouchers.show', ['type' => $type, 'voucher' => $voucher]) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $vouchers->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $vouchers->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $vouchers->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($vouchers->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $vouchers->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($vouchers->hasMorePages())
                    <a href="{{ $vouchers->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
