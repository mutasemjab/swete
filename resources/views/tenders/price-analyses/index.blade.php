@extends('layouts.app')

@section('title', __('tenders.price_analyses_list'))
@section('breadcrumb', __('tenders.price_analyses_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.price_analyses_list') }}</h1>
        <p class="page-subtitle">{{ __('tenders.price_analyses_subtitle') }}</p>
    </div>
    <a href="{{ route('price-analyses.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('tenders.add_price_analysis') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-chart-line text-orange-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $analyses->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('tenders.total_price_analyses') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('price-analyses.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="branch_id" class="js-select2 form-select w-56">
        <option value="">{{ __('tenders.all_branches') }}</option>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['branch_id']))
        <a href="{{ route('price-analyses.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($analyses->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-chart-line text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('tenders.no_price_analyses') }}</p>
            <a href="{{ route('price-analyses.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('tenders.add_first_price_analysis') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_branch') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_with_tax') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_subtotal') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($analyses as $analysis)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('price-analyses.show', $analysis) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $analysis->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $analysis->branch?->localized_name }}</td>
                        <td class="px-5 py-4">
                            @if($analysis->with_tax)
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('app.yes') }}</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">{{ __('app.no') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800" dir="ltr">{{ number_format($analysis->total_with_tax, 3) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('price-analyses.show', $analysis) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('price-analyses.edit', $analysis) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('price-analyses.destroy', $analysis) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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

        @if($analyses->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $analyses->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $analyses->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $analyses->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($analyses->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $analyses->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($analyses->hasMorePages())
                    <a href="{{ $analyses->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
