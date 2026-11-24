@extends('layouts.app')

@section('title', __('equipment.pt_points_list'))
@section('breadcrumb', __('equipment.pt_points_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.pt_points_list') }}</h1>
        <p class="page-subtitle">{{ __('equipment.pt_points_subtitle') }}</p>
    </div>
    <a href="{{ route('superheat-calculator.index') }}" class="btn-secondary">
        <i class="fa-solid fa-calculator"></i>
        {{ __('equipment.superheat_calculator') }}
    </a>
</div>

<form method="GET" action="{{ route('refrigerant-pt-points.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="refrigerant" class="js-select2 form-select w-48">
        <option value="">{{ __('equipment.pt_points_all_refrigerants') }}</option>
        @foreach($refrigerants as $refrigerant)
            <option value="{{ $refrigerant }}" @selected(request('refrigerant') == $refrigerant)>{{ $refrigerant }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> {{ __('app.search') }}</button>
</form>

@can('equipment.reading_limits.manage')
<form action="{{ route('refrigerant-pt-points.store') }}" method="POST" class="card px-5 py-4 mb-4 flex flex-wrap items-end gap-3">
    @csrf
    <div class="min-w-32">
        <label class="form-label">{{ __('equipment.pt_point_refrigerant') }}</label>
        <input type="text" name="refrigerant" dir="ltr" placeholder="R410A" required class="form-input">
    </div>
    <div class="min-w-32">
        <label class="form-label">{{ __('equipment.pt_point_temperature') }}</label>
        <input type="number" step="0.01" name="temperature_c" dir="ltr" required class="form-input">
    </div>
    <div class="min-w-32">
        <label class="form-label">{{ __('equipment.pt_point_pressure') }}</label>
        <input type="number" step="0.001" name="pressure_bar" dir="ltr" required class="form-input">
    </div>
    <div class="flex-1 min-w-40">
        <label class="form-label">{{ __('equipment.pt_point_source') }}</label>
        <input type="text" name="source" class="form-input">
    </div>
    <button type="submit" class="btn-secondary"><i class="fa-solid fa-plus"></i> {{ __('equipment.pt_point_add') }}</button>
</form>
@endcan

<div class="card overflow-hidden">
    @if($points->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <p class="text-slate-800 font-bold text-lg">{{ __('equipment.pt_points_none') }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ __('equipment.pt_points_none_hint') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.pt_point_refrigerant') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.pt_point_temperature') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.pt_point_pressure') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.pt_point_source') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($points as $point)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-3 font-bold text-slate-800" dir="ltr">{{ $point->refrigerant }}</td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr">{{ $point->temperature_c }}°C</td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr">{{ $point->pressure_bar }} bar</td>
                        <td class="px-5 py-3 text-xs text-slate-400">{{ $point->source }}</td>
                        <td class="px-5 py-3">
                            @can('equipment.reading_limits.manage')
                            <div class="flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('refrigerant-pt-points.destroy', $point) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
