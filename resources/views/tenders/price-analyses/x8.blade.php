@extends('layouts.app')

@section('title', __('tenders.x8_edit') . ' — ' . $analysis->number)
@section('breadcrumb', $analysis->number)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.x8_edit') }}</h1>
        <p class="page-subtitle">{{ $analysis->number }} — {{ $analysis->branch?->localized_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('price-analyses.x8.show', $analysis) }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.cancel') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success mb-5">{{ session('success') }}</div>
@endif

<form action="{{ route('price-analyses.x8.save', $analysis) }}" method="POST">
    @csrf

    <div class="card overflow-hidden mb-5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_model') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('tenders.analysis_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">L.P</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-36">{{ __('tenders.analysis_item_required_discount') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.notes') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($analysis->items as $index => $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <input type="hidden" name="x8[{{ $index }}][price_analysis_item_id]" value="{{ $item->id }}">
                            <span class="font-bold text-slate-800">{{ $item->material?->localized_name ?: '—' }}</span>
                            @if($item->ciat_model)
                            <span class="text-slate-400 text-sm ms-1" dir="ltr">({{ $item->ciat_model }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600" dir="ltr">
                            {{ number_format((float) $item->quantity, 3) }}
                        </td>
                        <td class="px-5 py-3 text-slate-600" dir="ltr">
                            {{ number_format((float) $item->list_price, 3) }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="relative">
                                <input type="number"
                                       name="x8[{{ $index }}][required_discount_percent]"
                                       value="{{ old('x8.'.$index.'.required_discount_percent', $item->x8?->required_discount_percent) }}"
                                       step="0.01" min="0" max="100" dir="ltr"
                                       class="form-input !pe-6 @error('x8.'.$index.'.required_discount_percent') is-invalid @enderror">
                                <span class="absolute top-1/2 -translate-y-1/2 end-2 text-slate-400 text-xs pointer-events-none">%</span>
                            </div>
                            @error('x8.'.$index.'.required_discount_percent')
                                <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </td>
                        <td class="px-5 py-3">
                            <input type="text"
                                   name="x8[{{ $index }}][notes]"
                                   value="{{ old('x8.'.$index.'.notes', $item->x8?->notes) }}"
                                   class="form-input w-full @error('x8.'.$index.'.notes') is-invalid @enderror">
                            @error('x8.'.$index.'.notes')
                                <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end gap-2 mb-5">
        <a href="{{ route('price-analyses.x8.show', $analysis) }}" class="btn-secondary">
            {{ __('app.cancel') }}
        </a>
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
    </div>
</form>
@endsection
