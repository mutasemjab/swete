@extends('layouts.app')

@section('title', __('equipment.alarm_lookup'))
@section('breadcrumb', __('equipment.alarm_lookup'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.alarm_lookup') }}</h1>
        <p class="page-subtitle">{{ __('equipment.alarm_lookup_subtitle') }}</p>
    </div>
    @can('equipment.alarm_codes.create')
    <a href="{{ route('alarm-codes.create') }}" class="btn-secondary">
        <i class="fa-solid fa-plus"></i>
        {{ __('equipment.add_alarm_code') }}
    </a>
    @endcan
</div>

<form method="GET" action="{{ route('alarm-lookup.index') }}" class="card px-6 py-5 mb-5 flex flex-wrap gap-3">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('equipment.alarm_lookup_placeholder') }}" class="form-input flex-1 min-w-56" autofocus>
    <select name="control_system_id" class="js-select2 form-select w-48">
        <option value="">{{ __('equipment.all_control_systems') }}</option>
        @foreach($controlSystems as $cs)
            <option value="{{ $cs->id }}" @selected(request('control_system_id') == $cs->id)>{{ $cs->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary"><i class="fa-solid fa-magnifying-glass"></i> {{ __('app.search') }}</button>
</form>

@if(! request()->filled('q'))
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-500 text-sm">{{ __('equipment.alarm_lookup_hint') }}</p>
    </div>
@elseif($results->isEmpty())
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-800 font-bold">{{ __('equipment.alarm_lookup_no_results') }}</p>
        <p class="text-sm text-slate-500 mt-1">{{ __('equipment.alarm_lookup_no_results_hint') }}</p>
    </div>
@else
    <p class="text-sm text-slate-500 mb-3">{{ __('equipment.alarm_lookup_results_from_manuals') }}</p>
    @foreach($results as $alarm)
    <div class="card px-6 py-5 mb-4">
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2">
                <span class="font-mono font-black text-slate-800 text-lg" dir="ltr">{{ $alarm->code }}</span>
                <span class="font-bold text-slate-700">{{ $alarm->name }}</span>
            </div>
            <span class="badge bg-cyan-100 text-cyan-700">{{ $alarm->controlSystem?->localized_name }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            @if($alarm->effect)
            <div><dt class="text-slate-400 font-medium mb-0.5">{{ __('equipment.alarm_effect') }}</dt><dd class="text-slate-700">{{ $alarm->effect }}</dd></div>
            @endif
            @if($alarm->causes)
            <div><dt class="text-slate-400 font-medium mb-0.5">{{ __('equipment.alarm_causes') }}</dt><dd class="text-slate-700">{{ $alarm->causes }}</dd></div>
            @endif
            @if($alarm->checks)
            <div><dt class="text-slate-400 font-medium mb-0.5">{{ __('equipment.alarm_checks') }}</dt><dd class="text-slate-700">{{ $alarm->checks }}</dd></div>
            @endif
            @if($alarm->action)
            <div><dt class="text-slate-400 font-medium mb-0.5">{{ __('equipment.alarm_action') }}</dt><dd class="text-slate-700">{{ $alarm->action }}</dd></div>
            @endif
            @if($alarm->reset)
            <div class="sm:col-span-2"><dt class="text-slate-400 font-medium mb-0.5">{{ __('equipment.alarm_reset') }}</dt><dd class="text-slate-700">{{ $alarm->reset }}</dd></div>
            @endif
        </div>

        <p class="text-xs text-slate-400 mt-4 pt-3 border-t border-slate-100">
            <i class="fa-solid fa-book"></i>
            {{ __('equipment.alarm_source') }}: {{ $alarm->sourceDocument?->localized_name }} — {{ __('equipment.alarm_page') }} {{ $alarm->source_page }}
        </p>
    </div>
    @endforeach
@endif
@endsection
