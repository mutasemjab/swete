@extends('layouts.app')

@section('title', $analysis->number)
@section('breadcrumb', $analysis->number)

@section('content')

@unless($analysis->is_current)
    <div class="card !bg-amber-50 !border-amber-200 px-5 py-4 mb-5 flex items-center justify-between gap-3">
        <p class="font-bold text-amber-800">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ __('tenders.analysis_old_version_banner') }}
        </p>
        @if($analysis->root)
            <a href="{{ route('price-analyses.show', $analysis->root) }}" class="btn-secondary btn-sm">
                {{ __('tenders.analysis_view_current_version') }}
            </a>
        @endif
    </div>
@endunless

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $analysis->number }}</h1>
        <p class="page-subtitle">{{ $analysis->branch?->localized_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($analysis->is_current)
            <a href="{{ route('price-analyses.edit', $analysis) }}" class="btn-secondary">
                <i class="fa-solid fa-pen"></i>
                {{ __('app.edit') }}
            </a>
        @endif
        <a href="{{ route('price-analyses.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

@if($analysis->is_current && $analysis->versions->isNotEmpty())
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.analysis_version_history') }}</h3>
    </div>
    <div class="px-6 py-4">
        @foreach($analysis->versions as $oldVersion)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <span class="text-sm text-slate-600">
                    <span class="font-mono font-bold" dir="ltr">{{ $oldVersion->number }}</span>
                    — <span dir="ltr">{{ $oldVersion->updated_at->format('Y-m-d H:i') }}</span>
                </span>
                <a href="{{ route('price-analyses.show', $oldVersion) }}" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.analysis_branch') }}</dt>
            <dd class="font-bold text-slate-800">{{ $analysis->branch?->localized_name }}</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.analysis_tax_rate') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ rtrim(rtrim(number_format($analysis->tax_rate, 2), '0'), '.') }}%</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.analysis_jd_rate') }}</dt>
            <dd class="font-bold text-slate-800" dir="ltr">{{ rtrim(rtrim(number_format($analysis->jd_rate, 4), '0'), '.') }}</dd>
        </div>
        @if($analysis->linked)
        <div>
            @if($analysis->linked_type === 'tender')
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.tender') }}</dt>
                <dd class="font-bold text-slate-800">
                    <a href="{{ route('tenders.show', $analysis->linked) }}" class="text-indigo-600 hover:underline">{{ $analysis->linked->number }} — {{ $analysis->linked->localized_title }}</a>
                </dd>
            @elseif($analysis->linked_type === 'project')
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.project') }}</dt>
                <dd class="font-bold text-slate-800">
                    <a href="{{ route('projects.show', $analysis->linked) }}" class="text-indigo-600 hover:underline">{{ $analysis->linked->number }} — {{ $analysis->linked->localized_title }}</a>
                </dd>
            @else
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.quote_number') }}</dt>
                <dd class="font-bold text-slate-800">
                    <a href="{{ route('price-quotes.show', $analysis->linked) }}" class="text-indigo-600 hover:underline">{{ $analysis->linked->number }}</a>
                </dd>
            @endif
        </div>
        @endif
        @if($analysis->notes)
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5">{{ __('tenders.analysis_notes') }}</dt>
            <dd class="text-slate-700">{{ $analysis->notes }}</dd>
        </div>
        @endif
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700">{{ __('tenders.analysis_item_product') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_source') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.ciat_type') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.ciat_model') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_quantity') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_list_price') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_cost') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_profit') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_total_profit') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_price') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_to_jd') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_shipping') }}</th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_subtotal') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($analysis->items as $item)
                <tr>
                    <td class="px-3 py-3">
                        <span class="badge {{ $item->source_type === 'ciat' ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $item->source_type === 'ciat' ? __('tenders.analysis_source_ciat') : __('tenders.analysis_source_warehouse') }}
                        </span>
                    </td>
                    <td class="px-3 py-3 font-bold text-slate-800">{{ $item->material?->localized_name }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ $item->ciat_model }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->list_price, 3) }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->cost, 3) }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->profit, 3) }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->total_profit, 3) }}</td>
                    <td class="px-3 py-3 font-bold text-slate-800" dir="ltr">{{ number_format($item->price, 3) }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->to_jd, 3) }}</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr">{{ number_format($item->shipping, 3) }}</td>
                    <td class="px-3 py-3 font-bold text-orange-700" dir="ltr">{{ number_format($item->subtotal, 3) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-96">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium">{{ __('tenders.analysis_item_subtotal') }}</dt>
                <dd class="font-bold text-slate-800" dir="ltr">{{ number_format($analysis->subtotal, 3) }}</dd>
            </div>
            @if($analysis->with_tax)
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <dt class="text-slate-700 font-bold">{{ __('tenders.analysis_total_with_tax') }}</dt>
                <dd class="font-black text-lg text-orange-700" dir="ltr">{{ number_format($analysis->total_with_tax, 3) }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection
