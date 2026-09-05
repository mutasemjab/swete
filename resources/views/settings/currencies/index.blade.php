@extends('layouts.app')

@section('title', __('settings.currencies_list'))
@section('breadcrumb', __('settings.currencies'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.currencies_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.currencies_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.currencies.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('settings.add_currency') }}
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-coins text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $currencies->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.total_currencies') }}</p>
        </div>
    </div>
    @php $default = $currencies->firstWhere('is_default', true); @endphp
    @if($default)
    <div class="card px-5 py-4 flex items-center gap-4 border-emerald-200">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <span class="text-emerald-700 font-black text-lg">{{ $default->symbol }}</span>
        </div>
        <div>
            <p class="text-sm font-black text-slate-800">{{ $default->localized_name }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.default_currency') }}</p>
        </div>
    </div>
    @endif
</div>

<div class="card overflow-hidden">
    @if($currencies->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-coins text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('settings.no_currencies') }}</p>
            <a href="{{ route('settings.currencies.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('settings.add_first_currency') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.currency_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.currency_code') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.currency_symbol') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('settings.exchange_rate') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($currencies as $currency)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl {{ $currency->is_default ? 'bg-emerald-100' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                                    <span class="{{ $currency->is_default ? 'text-emerald-700' : 'text-slate-600' }} font-black text-sm">
                                        {{ $currency->symbol }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $currency->localized_name }}</p>
                                    @if($currency->is_default)
                                        <span class="badge bg-emerald-100 text-emerald-700 text-[10px]">{{ __('settings.default_currency') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $currency->code }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm font-bold text-slate-700">{{ $currency->symbol }}</span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-600 font-mono" dir="ltr">{{ number_format($currency->exchange_rate, 4) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($currency->status)
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    {{ __('app.active') }}
                                </span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    {{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('settings.currencies.edit', $currency) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @if(!$currency->is_default)
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', {
                                            action: '{{ route('settings.currencies.destroy', $currency) }}',
                                            message: '{{ __('app.delete_confirm_msg') }}'
                                        })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                @else
                                <div class="p-1.5 text-slate-300 !cursor-not-allowed" title="{{ __('settings.is_default_hint') }}">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </div>
                                @endif
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
