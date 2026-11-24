@extends('layouts.app')

@section('title', __('equipment.models_list'))
@section('breadcrumb', __('equipment.models_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.models_list') }}</h1>
        <p class="page-subtitle">{{ __('equipment.models_subtitle') }}</p>
    </div>
    @can('equipment.models.create')
    <a href="{{ route('equipment-models.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('equipment.add_model') }}
    </a>
    @endcan
</div>

<form method="GET" action="{{ route('equipment-models.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="equipment_manufacturer_id" class="js-select2 form-select w-56">
        <option value="">{{ __('equipment.all_manufacturers') }}</option>
        @foreach($manufacturers as $manufacturer)
            <option value="{{ $manufacturer->id }}" @selected(request('equipment_manufacturer_id') == $manufacturer->id)>{{ $manufacturer->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['equipment_manufacturer_id']))
        <a href="{{ route('equipment-models.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($models->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-microchip text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('equipment.no_models') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.model_number') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.manufacturer') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.category') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($models as $model)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4 font-bold text-slate-800" dir="ltr">{{ $model->model_number }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $model->manufacturer?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $model->category?->localized_name }}
                            @if($model->subtype) <span class="text-slate-400">/ {{ $model->subtype->localized_name }}</span> @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($model->status)
                                <span class="badge bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('app.active') }}</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500"><span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>{{ __('app.inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('equipment.models.edit')
                                <a href="{{ route('equipment-models.edit', $model) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @endcan
                                @can('equipment.models.delete')
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('equipment-models.destroy', $model) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($models->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $models->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $models->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $models->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($models->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $models->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($models->hasMorePages())
                    <a href="{{ $models->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
