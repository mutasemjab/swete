@extends('layouts.app')

@section('title', __('maintenance.search_similar_problems'))
@section('breadcrumb', __('maintenance.search_similar_problems'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.search_similar_problems') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.search_similar_problems_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('maintenance-reports.search') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <input type="text" name="q" value="{{ old('q', request('q')) }}" autofocus
           class="form-input flex-1 min-w-[240px]" placeholder="{{ __('maintenance.search_placeholder') }}">
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-magnifying-glass"></i>
        {{ __('app.search') }}
    </button>
</form>

@if(! request()->filled('q'))
    <div class="card px-6 py-16 text-center">
        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5 mx-auto">
            <i class="fa-solid fa-magnifying-glass text-slate-400 text-3xl"></i>
        </div>
        <p class="text-slate-800 font-bold text-lg">{{ __('maintenance.search_no_query') }}</p>
    </div>
@elseif($reports->isEmpty())
    <div class="card px-6 py-16 text-center">
        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5 mx-auto">
            <i class="fa-solid fa-clipboard-question text-slate-400 text-3xl"></i>
        </div>
        <p class="text-slate-800 font-bold text-lg">{{ __('maintenance.search_no_results') }}</p>
    </div>
@else
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_problem') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_solution') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reports as $report)
                    <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('maintenance-reports.show', $report) }}'">
                        <td class="px-5 py-4">
                            <a href="{{ route('maintenance-reports.show', $report) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $report->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $report->customer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600 max-w-xs truncate">{{ $report->problem }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600 max-w-xs truncate">{{ $report->solution }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500" dir="ltr">{{ $report->date->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $reports->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $reports->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $reports->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($reports->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $reports->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($reports->hasMorePages())
                    <a href="{{ $reports->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    </div>
@endif
@endsection
