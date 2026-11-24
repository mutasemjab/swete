@extends('layouts.app')

@section('title', __('equipment.superheat_calculator'))
@section('breadcrumb', __('equipment.superheat_calculator'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.superheat_calculator') }}</h1>
        <p class="page-subtitle">{{ __('equipment.superheat_calculator_subtitle') }}</p>
    </div>
    @can('equipment.reading_limits.manage')
    <a href="{{ route('refrigerant-pt-points.index') }}" class="btn-secondary">
        <i class="fa-solid fa-table"></i>
        {{ __('equipment.pt_points_manage') }}
    </a>
    @endcan
</div>

@if(empty($refrigerants))
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-800 font-bold text-lg mb-2">{{ __('equipment.superheat_no_refrigerants') }}</p>
        <p class="text-sm text-slate-500 mb-4">{{ __('equipment.superheat_no_refrigerants_hint') }}</p>
        @can('equipment.reading_limits.manage')
        <a href="{{ route('refrigerant-pt-points.index') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            {{ __('equipment.pt_points_manage') }}
        </a>
        @endcan
    </div>
@else
<form method="GET" action="{{ route('superheat-calculator.index') }}" class="card px-6 py-5 mb-5 grid grid-cols-1 sm:grid-cols-4 gap-4">
    @if($unit)<input type="hidden" name="equipment_unit_id" value="{{ $unit->id }}">@endif
    <div>
        <label class="form-label">{{ __('equipment.superheat_refrigerant') }}</label>
        <select name="refrigerant" class="js-select2 form-select" required>
            @foreach($refrigerants as $refrigerant)
                <option value="{{ $refrigerant }}" @selected(request('refrigerant') == $refrigerant)>{{ $refrigerant }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">{{ __('equipment.superheat_suction_temp') }}</label>
        <input type="number" step="0.1" name="suction_temp_c" value="{{ request('suction_temp_c') }}" dir="ltr" required class="form-input">
    </div>
    <div>
        <label class="form-label">{{ __('equipment.superheat_suction_pressure') }}</label>
        <div class="flex gap-2">
            <input type="number" step="0.1" name="suction_pressure" value="{{ request('suction_pressure') }}" dir="ltr" required class="form-input">
            <select name="pressure_unit" class="form-select w-24">
                @foreach(['bar', 'psi', 'kpa', 'mpa'] as $pu)
                    <option value="{{ $pu }}" @selected(request('pressure_unit', 'bar') == $pu)>{{ strtoupper($pu) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex items-end">
        <button type="submit" class="btn-primary w-full">
            <i class="fa-solid fa-calculator"></i>
            {{ __('equipment.superheat_calculate') }}
        </button>
    </div>
</form>

@if($unit)
<p class="text-sm text-slate-500 mb-4">{{ __('equipment.superheat_for_unit') }}: <span class="font-bold text-slate-700">{{ $unit->localized_name }}</span></p>
@endif

<div class="card px-6 py-5 mb-5">
    <p class="text-sm text-slate-500">{{ __('equipment.superheat_band_in_use') }}: <span class="font-bold text-slate-700" dir="ltr">{{ $band[0] }}K – {{ $band[1] }}K</span></p>
</div>

@if($result)
    <div class="card px-6 py-8 text-center">
        @if($result['message'])
            <p class="text-slate-800 font-bold text-lg mb-2">{{ $result['message'] }}</p>
        @else
            @php($zoneColors = ['low' => 'amber', 'normal' => 'emerald', 'high' => 'rose'])
            @php($zoneColor = $zoneColors[$result['zone']])
            <p class="text-5xl font-black text-{{ $zoneColor }}-600" dir="ltr">{{ $result['superheat'] }}K</p>
            <p class="text-sm text-slate-500 mt-2">{{ __('equipment.superheat_saturation_temp') }}: <span dir="ltr">{{ $result['saturation_temp'] }}°C</span></p>
            <span class="badge bg-{{ $zoneColor }}-100 text-{{ $zoneColor }}-700 mt-4">{{ __('equipment.superheat_zone_'.$result['zone']) }}</span>
        @endif
    </div>
@endif
@endif
@endsection
