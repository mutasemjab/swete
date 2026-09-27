@extends('layouts.app')

@section('title', __('maintenance.reports_list'))
@section('breadcrumb', __('maintenance.reports_list'))

@section('content')
@php $filterKeys = ['customer_id', 'template_id']; @endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.reports_list') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.reports_subtitle') }}</p>
    </div>
    <a href="{{ route('maintenance-reports.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('maintenance.add_report') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clipboard-list text-teal-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $reports->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('maintenance.total_reports') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('maintenance-reports.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="customer_id" class="js-select2 form-select w-56">
        <option value="">{{ __('maintenance.all_customers') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->localized_name }}</option>
        @endforeach
    </select>
    <select name="template_id" class="js-select2 form-select w-56">
        <option value="">{{ __('maintenance.all_templates') }}</option>
        @foreach($templates as $template)
            <option value="{{ $template->id }}" @selected(request('template_id') == $template->id)>{{ $template->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny($filterKeys))
        <a href="{{ route('maintenance-reports.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($reports->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-clipboard-list text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                {{ request()->hasAny($filterKeys) ? __('maintenance.no_reports_search') : __('maintenance.no_reports') }}
            </p>
            @if(!request()->hasAny($filterKeys))
                <a href="{{ route('maintenance-reports.create') }}" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('maintenance.add_first_report') }}
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('maintenance.report_product') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('maintenance.report_date') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reports as $report)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('maintenance-reports.show', $report) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $report->number }}</a>
                            <p class="text-xs text-slate-400">{{ $report->template_name }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $report->customer?->localized_name }}</td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $report->material?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $report->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('maintenance-reports.show', $report) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('maintenance-reports.edit', $report) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('maintenance-reports.destroy', $report) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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
    @endif
</div>
@endsection
