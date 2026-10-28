@extends('layouts.app')

@section('title', $x8->number)
@section('breadcrumb', $x8->number)

@section('content')
@php $tender = $x8->priceAnalysis?->linked; @endphp
<div class="page-header">
    <div>
        <h1 class="page-title">
            {{ $x8->number }}
            @unless($x8->is_current)
                <span class="badge bg-slate-100 text-slate-500">{{ __('tenders.quote_old_version_badge') }}</span>
            @endunless
        </h1>
        <p class="page-subtitle">
            {{ $x8->priceAnalysis?->number }}
            @if($tender)
                — <a href="{{ route('tenders.show', $tender) }}" class="text-indigo-600 hover:underline">{{ $tender->number }}</a>
            @endif
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('x8.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success mb-5">{{ session('success') }}</div>
@endif

@if($x8->is_current && $x8->versions->isNotEmpty())
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('x8.version_history') }}</h3>
    </div>
    <div class="px-6 py-4">
        @foreach($x8->versions as $oldVersion)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <span class="text-sm text-slate-600">
                    <span class="font-mono font-bold" dir="ltr">{{ $oldVersion->number }}</span>
                    — <span dir="ltr">{{ $oldVersion->updated_at->format('Y-m-d H:i') }}</span>
                </span>
                <a href="{{ route('x8.show', $oldVersion) }}" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_product') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_model') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_quantity') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_list_price') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_cost_price') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_required_discount') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.notes') }}</th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_status') }}</th>
                    <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($x8->priceAnalysis->items as $item)
                @php
                    // This header's OWN item row, never the item's always-current x8 relation —
                    // a historical/archived header must show what the decision was back THEN.
                    $x8Item = $x8->items->firstWhere('price_analysis_item_id', $item->id);
                    $status = $x8Item?->approval_status ?? 'pending';
                    $canDecide = $x8->is_current && $status !== 'approved';
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors" @if($canDecide) x-data="{ rejecting: false }" @endif>
                    <td class="px-5 py-4 font-bold text-slate-800">{{ $item->material?->localized_name ?: '—' }}</td>
                    <td class="px-5 py-4 text-slate-600" dir="ltr">{{ $item->ciat_model ?: '—' }}</td>
                    <td class="px-5 py-4 text-slate-600" dir="ltr">{{ number_format((float) $item->quantity, 3) }}</td>
                    <td class="px-5 py-4 text-slate-600" dir="ltr">{{ number_format((float) $item->list_price, 3) }}</td>
                    <td class="px-5 py-4 text-slate-600" dir="ltr">{{ number_format((float) $item->cost_price, 3) }}</td>
                    <td class="px-5 py-4 text-slate-600" dir="ltr" @if($canDecide) x-show="!rejecting" @endif>{{ $x8Item?->required_discount_percent !== null ? number_format((float) $x8Item->required_discount_percent, 2) . '%' : '—' }}</td>
                    <td class="px-5 py-4 text-slate-600" @if($canDecide) x-show="!rejecting" @endif>
                        @forelse($x8Item?->notes ?? [] as $note)
                            <div class="text-sm">{{ $note }}</div>
                        @empty
                            —
                        @endforelse
                    </td>
                    <td class="px-5 py-4" @if($canDecide) x-show="!rejecting" @endif>
                        @if($status === 'approved')
                            <span class="badge bg-emerald-100 text-emerald-700">{{ __('x8.status_approved') }}</span>
                        @elseif($status === 'rejected')
                            <span class="badge bg-rose-100 text-rose-700">{{ __('x8.status_rejected') }}</span>
                        @else
                            <span class="badge bg-amber-100 text-amber-700">{{ __('x8.status_pending') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4" @if($canDecide) x-show="!rejecting" @endif>
                        @if($canDecide)
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
                        @endif
                    </td>
                    @if($canDecide)
                    <template x-if="rejecting">
                        <td colspan="4" class="px-5 py-4">
                            <form action="{{ route('x8.reject', $item) }}" method="POST" class="flex items-end gap-2 flex-wrap">
                                @csrf
                                <div>
                                    <label class="form-label">{{ __('x8.reject_new_discount_label') }}</label>
                                    <input type="number" step="0.01" min="0" max="100" name="required_discount_percent"
                                           value="{{ $x8Item?->required_discount_percent }}" class="form-input w-32" required>
                                </div>
                                <button type="submit" class="btn-danger btn-sm">{{ __('x8.reject_submit') }}</button>
                                <button type="button" @click="rejecting = false" class="btn-secondary btn-sm">{{ __('app.cancel') }}</button>
                            </form>
                        </td>
                    </template>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
