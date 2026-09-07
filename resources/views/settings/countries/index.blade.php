@extends('layouts.app')

@section('title', __('settings.countries_list'))
@section('breadcrumb', __('settings.countries'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.countries_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.countries_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.countries.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('settings.add_country') }}
    </a>
</div>

<div class="card overflow-hidden">
    @if($countries->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-earth-americas text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('settings.no_countries') }}</p>
            <a href="{{ route('settings.countries.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('settings.add_first_country') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.country_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($countries as $country)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $country->localized_name }}</p></td>
                        <td class="px-5 py-4">
                            @if($country->status)
                                <span class="badge bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('app.active') }}</span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500"><span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>{{ __('app.inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('settings.countries.edit', $country) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('settings.countries.destroy', $country) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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
    @endif
</div>
@endsection
