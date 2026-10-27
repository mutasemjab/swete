@extends('layouts.app')

@section('title', __('x8.index_title'))
@section('breadcrumb', __('x8.index_title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('x8.index_title') }}</h1>
        <p class="page-subtitle">{{ __('x8.index_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('x8.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="status" class="form-select w-56">
        <option value="">{{ __('x8.filter_all') }}</option>
        <option value="pending" @selected(request('status') === 'pending')>{{ __('x8.status_pending') }}</option>
        <option value="approved" @selected(request('status') === 'approved')>{{ __('x8.status_approved') }}</option>
        <option value="rejected" @selected(request('status') === 'rejected')>{{ __('x8.status_rejected') }}</option>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['status']))
        <a href="{{ route('x8.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($items->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-square-check text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('x8.no_items') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_tender') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_model') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_quantity') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_list_price') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_cost_price') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_required_discount') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($items as $item)
                    @php
                        $status = $item->x8?->approval_status ?? 'pending';
                        $tender = $item->priceAnalysis?->linked;
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ rejecting: false }">
                        <td class="px-5 py-4">
                            @if($tender)
                                <a href="{{ route('tenders.show', $tender) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $tender->number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800">{{ $item->ciat_model ?: $item->material?->localized_name ?: '—' }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-600" dir="ltr">{{ number_format((float) $item->quantity, 3) }}</td>
                        <td class="px-5 py-4 text-slate-600" dir="ltr">{{ number_format((float) $item->list_price, 3) }}</td>
                        <td class="px-5 py-4 text-slate-600" dir="ltr">{{ number_format((float) $item->cost_price, 3) }}</td>
                        <td class="px-5 py-4 text-slate-600" dir="ltr" x-show="!rejecting">{{ $item->x8?->required_discount_percent !== null ? number_format((float) $item->x8->required_discount_percent, 2) . '%' : '—' }}</td>
                        <td class="px-5 py-4" x-show="!rejecting">
                            @if($status === 'approved')
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('x8.status_approved') }}</span>
                            @elseif($status === 'rejected')
                                <span class="badge bg-rose-100 text-rose-700">{{ __('x8.status_rejected') }}</span>
                            @else
                                <span class="badge bg-amber-100 text-amber-700">{{ __('x8.status_pending') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4" x-show="!rejecting">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('x8.approve', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm">
                                        <i class="fa-solid fa-check"></i> {{ __('x8.approve') }}
                                    </button>
                                </form>
                                <button type="button" @click="rejecting = true" class="btn-danger btn-sm">
                                    <i class="fa-solid fa-xmark"></i> {{ __('x8.reject') }}
                                </button>
                            </div>
                        </td>
                        <template x-if="rejecting">
                            <td colspan="3" class="px-5 py-4">
                                <form action="{{ route('x8.reject', $item) }}" method="POST" class="flex items-end gap-2 flex-wrap">
                                    @csrf
                                    <div>
                                        <label class="form-label">{{ __('x8.reject_new_discount_label') }}</label>
                                        <input type="number" step="0.01" min="0" max="100" name="required_discount_percent"
                                               value="{{ $item->x8?->required_discount_percent }}" class="form-input w-32" required>
                                    </div>
                                    <button type="submit" class="btn-danger btn-sm">{{ __('x8.reject_submit') }}</button>
                                    <button type="button" @click="rejecting = false" class="btn-secondary btn-sm">{{ __('app.cancel') }}</button>
                                </form>
                            </td>
                        </template>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $items->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $items->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $items->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($items->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $items->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($items->hasMorePages())
                    <a href="{{ $items->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
