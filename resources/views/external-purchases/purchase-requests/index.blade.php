@extends('layouts.app')

@section('title', __('external_purchases.requests_list'))
@section('breadcrumb', __('external_purchases.purchase_requests'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.requests_list') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.requests_subtitle') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('purchase-requests.ship') }}" class="btn-secondary">
            <i class="fa-solid fa-ship"></i>
            {{ __('external_purchases.send_to_shipping_companies') }}
        </a>
        <a href="{{ route('purchase-requests.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            {{ __('external_purchases.add_purchase_request') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-truck-ramp-box text-cyan-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $purchaseRequests->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('external_purchases.total_requests') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('purchase-requests.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search']))
        <a href="{{ route('purchase-requests.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($purchaseRequests->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-truck-ramp-box text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                {{ request()->hasAny(['search']) ? __('external_purchases.no_requests_search') : __('external_purchases.no_requests') }}
            </p>
            @if(!request()->hasAny(['search']))
                <a href="{{ route('purchase-requests.create') }}" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('external_purchases.add_first_request') }}
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_supplier') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('external_purchases.request_linked_to') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('external_purchases.request_total') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchaseRequests as $pr)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('purchase-requests.show', $pr) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $pr->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $pr->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $pr->supplier?->localized_name }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                            @if($pr->project)
                                {{ __('external_purchases.link_type_project') }}: {{ $pr->project->number }}
                            @elseif($pr->serviceCall)
                                {{ __('external_purchases.link_type_service_call') }}: {{ $pr->serviceCall->number }}
                            @else
                                {{ __('external_purchases.link_type_stock') }}
                            @endif
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800">{{ number_format($pr->total, 3) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('purchase-requests.show', $pr) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                @if($pr->isEditable())
                                <a href="{{ route('purchase-requests.edit', $pr) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @endif
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('purchase-requests.destroy', $pr) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($purchaseRequests->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $purchaseRequests->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $purchaseRequests->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $purchaseRequests->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($purchaseRequests->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $purchaseRequests->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($purchaseRequests->hasMorePages())
                    <a href="{{ $purchaseRequests->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
