@php $routePrefix = $type === 'service_call' ? 'service-calls' : 'tenders'; @endphp
@extends('layouts.app')

@section('title', __('tenders.list_' . $type))
@section('breadcrumb', __('tenders.type_' . $type . '_plural'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.list_' . $type) }}</h1>
        <p class="page-subtitle">{{ __('tenders.tenders_subtitle') }}</p>
    </div>
    <a href="{{ route("{$routePrefix}.create") }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('tenders.add_' . $type) }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-gavel text-orange-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $tenders->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('tenders.total_tenders') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route("{$routePrefix}.index") }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <select name="status" class="form-select w-44">
        <option value="">{{ __('tenders.all_statuses_tender') }}</option>
        @foreach(['open','closed','won','lost'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('tenders.tender_status_' . $status) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route("{$routePrefix}.index") }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($tenders->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-gavel text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                {{ request()->hasAny(['search','status']) ? __('tenders.no_tenders_search') : __('tenders.no_tenders') }}
            </p>
            @if(!request()->hasAny(['search','status']))
                <a href="{{ route("{$routePrefix}.create") }}" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('tenders.add_first_tender') }}
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.tender_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.tender_title') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('tenders.tender_entity_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell">{{ __('tenders.tender_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.tender_submission_deadline') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.tender_status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $statusStyles = [
                            'open'   => 'bg-blue-100 text-blue-700',
                            'closed' => 'bg-slate-100 text-slate-500',
                            'won'    => 'bg-emerald-100 text-emerald-700',
                            'lost'   => 'bg-rose-100 text-rose-700',
                        ];
                    @endphp
                    @foreach($tenders as $tender)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $tender->number }}</span>
                        </td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $tender->localized_title }}</p></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $tender->localized_entity_name }}</td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600">{{ $tender->party?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $tender->submission_deadline->format('Y-m-d') }}</td>
                        <td class="px-5 py-4">
                            <span class="badge {{ $statusStyles[$tender->status] }}">{{ __('tenders.tender_status_' . $tender->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route("{$routePrefix}.edit", $tender) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route("{$routePrefix}.destroy", $tender) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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

        @if($tenders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $tenders->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $tenders->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $tenders->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($tenders->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $tenders->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($tenders->hasMorePages())
                    <a href="{{ $tenders->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
