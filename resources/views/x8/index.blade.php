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
        <a href="{{ route('x8.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($headers->isEmpty())
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
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_tender') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('x8.col_item_count') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.created_at') }}</th>
                        <th class="px-5 py-3.5 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($headers as $header)
                    @php $tender = $header->priceAnalysis?->linked; @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('x8.show', $header) }}" class="font-mono font-bold text-indigo-600 hover:underline">{{ $header->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $header->priceAnalysis?->number }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            @if($tender)
                                <a href="{{ route('tenders.show', $tender) }}" class="text-indigo-600 hover:underline">{{ $tender->number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $header->items->count() }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $header->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 text-end">
                            <a href="{{ route('x8.show', $header) }}" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i> {{ __('app.view') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($headers->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $headers->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $headers->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $headers->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($headers->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $headers->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($headers->hasMorePages())
                    <a href="{{ $headers->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
