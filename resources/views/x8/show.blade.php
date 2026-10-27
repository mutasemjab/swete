@extends('layouts.app')

@section('title', $x8->number)
@section('breadcrumb', $x8->number)

@section('content')
@php $tender = $x8->priceAnalysis?->linked; @endphp
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $x8->number }}</h1>
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

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
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
                @foreach($x8->priceAnalysis->items as $item)
                @php $status = $item->x8?->approval_status ?? 'pending'; @endphp
                <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ rejecting: false }">
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
</div>
@endsection
