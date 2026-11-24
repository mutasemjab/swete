@extends('layouts.app')

@section('title', __('equipment.alarm_codes_list'))
@section('breadcrumb', __('equipment.alarm_codes_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.alarm_codes_list') }}</h1>
        <p class="page-subtitle">{{ __('equipment.alarm_codes_subtitle') }}</p>
    </div>
    @can('equipment.alarm_codes.create')
    <a href="{{ route('alarm-codes.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('equipment.add_alarm_code') }}
    </a>
    @endcan
</div>

<form method="GET" action="{{ route('alarm-codes.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('equipment.alarm_search_placeholder') }}" class="form-input w-56">
    <select name="control_system_id" class="js-select2 form-select w-48">
        <option value="">{{ __('equipment.all_control_systems') }}</option>
        @foreach($controlSystems as $cs)
            <option value="{{ $cs->id }}" @selected(request('control_system_id') == $cs->id)>{{ $cs->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> {{ __('app.search') }}</button>
</form>

<div class="card overflow-hidden">
    @if($alarms->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <p class="text-slate-800 font-bold text-lg">{{ __('equipment.no_alarm_codes') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.alarm_code_value') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.alarm_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.control_system') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($alarms as $alarm)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-3 font-bold text-slate-800" dir="ltr">{{ $alarm->code }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $alarm->name }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600">{{ $alarm->controlSystem?->localized_name }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('equipment.alarm_codes.edit')
                                <a href="{{ route('alarm-codes.edit', $alarm) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"><i class="fa-solid fa-pen text-sm"></i></a>
                                @endcan
                                @can('equipment.alarm_codes.delete')
                                <button type="button" @click="$dispatch('delete-confirm', { action: '{{ route('alarm-codes.destroy', $alarm) }}', message: '{{ __('app.delete_confirm_msg') }}' })" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all"><i class="fa-solid fa-trash text-sm"></i></button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($alarms->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $alarms->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $alarms->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $alarms->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($alarms->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $alarms->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($alarms->hasMorePages())
                    <a href="{{ $alarms->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
