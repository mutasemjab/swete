@extends('layouts.app')

@section('title', __('equipment.units_list'))
@section('breadcrumb', __('equipment.units_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.units_list') }}</h1>
        <p class="page-subtitle">{{ __('equipment.units_subtitle') }}</p>
    </div>
    @can('equipment.units.create')
    <a href="{{ route('equipment-units.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('equipment.add_unit') }}
    </a>
    @endcan
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-server text-cyan-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $units->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('equipment.total_units') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('equipment-units.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('equipment.unit_search_placeholder') }}"
           class="form-input w-56">
    <select name="customer_id" class="js-select2 form-select w-48">
        <option value="">{{ __('equipment.all_customers') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->localized_name }}</option>
        @endforeach
    </select>
    <select name="equipment_category_id" class="js-select2 form-select w-48">
        <option value="">{{ __('equipment.all_categories') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('equipment_category_id') == $category->id)>{{ $category->localized_name }}</option>
        @endforeach
    </select>
    <select name="status" class="js-select2 form-select w-40">
        <option value="">{{ __('equipment.all_statuses') }}</option>
        @foreach(\App\Models\EquipmentUnit::STATUSES as $statusOption)
            <option value="{{ $statusOption }}" @selected(request('status') == $statusOption)>{{ __('equipment.unit_status_'.$statusOption) }}</option>
        @endforeach
    </select>
    <label class="flex items-center gap-2 text-sm text-slate-600 px-2">
        <input type="checkbox" name="show_archived" value="1" @checked(request()->boolean('show_archived'))>
        {{ __('equipment.show_archived') }}
    </label>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search', 'customer_id', 'equipment_category_id', 'status', 'show_archived']))
        <a href="{{ route('equipment-units.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($units->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-server text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('equipment.no_units') }}</p>
            @can('equipment.units.create')
            <a href="{{ route('equipment-units.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('equipment.add_first_unit') }}
            </a>
            @endcan
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.unit_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.unit_customer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.category') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.model') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.unit_status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($units as $unit)
                    <tr class="hover:bg-slate-50/50 transition-colors group {{ ! $unit->is_active ? 'opacity-50' : '' }}">
                        <td class="px-5 py-4">
                            <a href="{{ route('equipment-units.show', $unit) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition-colors">{{ $unit->localized_name }}</a>
                            @if($unit->serial_number)<p class="text-xs text-slate-400" dir="ltr">{{ $unit->serial_number }}</p>@endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $unit->customer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $unit->category?->localized_name }}
                            @if($unit->subtype) <span class="text-slate-400">/ {{ $unit->subtype->localized_name }}</span> @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600" dir="ltr">{{ $unit->model?->display_name }}</td>
                        <td class="px-5 py-4">
                            @php($statusColors = ['operational' => 'emerald', 'maintenance' => 'amber', 'out_of_service' => 'rose', 'decommissioned' => 'slate'])
                            @php($statusColor = $statusColors[$unit->status] ?? 'slate')
                            <span class="badge bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700"><span class="w-1.5 h-1.5 bg-{{ $statusColor }}-500 rounded-full"></span>{{ __('equipment.unit_status_'.$unit->status) }}</span>
                            @if(! $unit->is_active)
                                <span class="badge bg-slate-100 text-slate-500 ms-1">{{ __('equipment.archived') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('equipment.units.edit')
                                <a href="{{ route('equipment-units.edit', $unit) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @endcan
                                @can('equipment.units.delete')
                                @if($unit->is_active)
                                <button type="button" title="{{ __('equipment.archive') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('equipment-units.destroy', $unit) }}', message: '{{ __('equipment.archive_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-box-archive text-sm"></i>
                                </button>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($units->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $units->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $units->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $units->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($units->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $units->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($units->hasMorePages())
                    <a href="{{ $units->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
