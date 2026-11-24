@extends('layouts.app')

@section('title', __('equipment.subtypes_list'))
@section('breadcrumb', __('equipment.subtypes_list'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.subtypes_list') }}</h1>
        <p class="page-subtitle">{{ __('equipment.subtypes_subtitle') }}</p>
    </div>
    @can('equipment.categories.create')
    <a href="{{ route('equipment-subtypes.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('equipment.add_subtype') }}
    </a>
    @endcan
</div>

<div class="card overflow-hidden">
    @if($subtypes->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-sitemap text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('equipment.no_subtypes') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.subtype_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('equipment.category') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($subtypes as $subtype)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4 font-bold text-slate-800">{{ $subtype->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $subtype->category?->localized_name }}</td>
                        <td class="px-5 py-4">
                            @if($subtype->status)
                                <span class="badge bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('app.active') }}</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500"><span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>{{ __('app.inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('equipment.categories.edit')
                                <a href="{{ route('equipment-subtypes.edit', $subtype) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @endcan
                                @can('equipment.categories.delete')
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('equipment-subtypes.destroy', $subtype) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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
    @endif
</div>
@endsection
