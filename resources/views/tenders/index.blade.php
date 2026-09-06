@extends('layouts.app')

@section('title', __('tenders.tenders_list'))
@section('breadcrumb', __('tenders.tenders'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.tenders_list') }}</h1>
        <p class="page-subtitle">{{ __('tenders.tenders_subtitle') }}</p>
    </div>
    <a href="{{ route('tenders.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('tenders.add_tender') }}
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

<form method="GET" action="{{ route('tenders.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <select name="status_id" class="form-select w-52">
        <option value="">{{ __('tenders.all_statuses_tender') }}</option>
        @foreach($statuses as $statusOption)
            <option value="{{ $statusOption->id }}" @selected(request('status_id') == $statusOption->id)>{{ $statusOption->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search','status_id']))
        <a href="{{ route('tenders.index') }}" class="btn-secondary">
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
                {{ request()->hasAny(['search','status_id']) ? __('tenders.no_tenders_search') : __('tenders.no_tenders') }}
            </p>
            @if(!request()->hasAny(['search','status_id']))
                <a href="{{ route('tenders.create') }}" class="btn-primary mt-5">
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
                    @foreach($tenders as $tender)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="{{ route('tenders.show', $tender) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $tender->number }}</a>
                        </td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $tender->localized_title }}</p></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $tender->localized_entity_name }}</td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600">{{ $tender->party?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $tender->submission_deadline->format('Y-m-d') }}</td>
                        <td class="px-5 py-4">
                            <span class="badge bg-{{ $tender->statusRef?->color ?? 'slate' }}-100 text-{{ $tender->statusRef?->color ?? 'slate' }}-700">{{ $tender->statusRef?->localized_name }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('tenders.show', $tender) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('tenders.edit', $tender) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('tenders.destroy', $tender) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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
