@extends('layouts.app')

@section('title', __('split_units.list'))
@section('breadcrumb', __('split_units.split_units'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('split_units.list') }}</h1>
        <p class="page-subtitle">{{ __('split_units.subtitle') }}</p>
    </div>
    <a href="{{ route('split-units.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('split_units.add') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-file-invoice text-sky-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $splitUnits->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('split_units.total') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('split-units.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="customer_id" class="js-select2 form-select w-56">
        <option value="">{{ __('accounting.customer') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->localized_name }}</option>
        @endforeach
    </select>
    <select name="assigned_to" class="js-select2 form-select w-56">
        <option value="">{{ __('split_units.all_employees') }}</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" @selected(request('assigned_to') == $employee->id)>{{ $employee->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['customer_id', 'assigned_to']))
        <a href="{{ route('split-units.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($splitUnits->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-snowflake text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('split_units.no_quotes') }}</p>
            <a href="{{ route('split-units.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('split_units.add') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('split_units.number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('split_units.date') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('split_units.total') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('split_units.assigned_to') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($splitUnits as $splitUnit)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('split-units.show', $splitUnit) }}" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors">{{ $splitUnit->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $splitUnit->customer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $splitUnit->date->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 font-bold text-slate-800">{{ number_format($splitUnit->total, 3) }}</td>
                        <td class="px-5 py-4">
                            @if($splitUnit->invoice_id)
                                <span class="badge bg-emerald-100 text-emerald-700">{{ __('split_units.status_invoiced') }}</span>
                            @elseif($splitUnit->assignee)
                                <span class="badge bg-violet-100 text-violet-700">{{ $splitUnit->assignee->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">{{ __('split_units.unassigned') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('split-units.show', $splitUnit) }}"
                                   class="p-1.5 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('split-units.edit', $splitUnit) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('split-units.destroy', $splitUnit) }}" method="POST"
                                      onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="{{ __('app.delete') }}">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($splitUnits->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $splitUnits->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $splitUnits->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $splitUnits->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($splitUnits->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $splitUnits->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($splitUnits->hasMorePages())
                    <a href="{{ $splitUnits->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
