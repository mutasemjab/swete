@extends('layouts.app')

@section('title', __('settings.edit_currency'))
@section('breadcrumb', __('settings.edit_currency'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.edit_currency') }}</h1>
        <p class="page-subtitle">{{ $currency->localized_name }}</p>
    </div>
    <a href="{{ route('settings.currencies.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('settings.currencies.update', $currency) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-coins text-indigo-500 text-sm"></i>
                {{ __('settings.currency') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="form-label">{{ __('settings.currency_name') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $currency->name) }}"
                       class="form-input @error('name') is-invalid @enderror">
                @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.name_en') }}</label>
                <input type="text" name="name_en" value="{{ old('name_en', $currency->name_en) }}" dir="ltr"
                       class="form-input @error('name_en') is-invalid @enderror">
                @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('settings.currency_code') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $currency->code) }}" dir="ltr"
                       maxlength="3"
                       class="form-input uppercase @error('code') is-invalid @enderror">
                @error('code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('settings.currency_symbol') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}" dir="ltr"
                       class="form-input @error('symbol') is-invalid @enderror">
                @error('symbol')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('settings.exchange_rate') }} <span class="text-rose-500">*</span></label>
                <input type="number" name="exchange_rate" value="{{ old('exchange_rate', $currency->exchange_rate) }}"
                       step="0.0001" min="0" dir="ltr"
                       class="form-input @error('exchange_rate') is-invalid @enderror">
                <p class="text-xs text-slate-400 mt-1.5">{{ __('settings.exchange_rate_hint') }}</p>
                @error('exchange_rate')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-4 pt-2">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="is_default" value="0">
                    <input type="checkbox" name="is_default" value="1" class="sr-only peer"
                           @checked(old('is_default', $currency->is_default))>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-emerald-400 rounded-full peer
                                peer-checked:bg-emerald-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('settings.is_default') }}</span>
                </label>
                <p class="text-xs text-slate-400 -mt-2">{{ __('settings.is_default_hint') }}</p>

                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           @checked(old('status', $currency->status))>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
                </label>
            </div>
        </div>
    </div>

    @if(!$currency->is_default)
    <div class="card mb-5 border-rose-200">
        <div class="card-header border-rose-100">
            <h3 class="font-bold text-rose-700 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                {{ __('app.danger_zone') }}
            </h3>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <p class="text-xs text-slate-400">{{ __('app.irreversible') }}</p>
            <button type="button"
                    @click="$dispatch('delete-confirm', {
                        action: '{{ route('settings.currencies.destroy', $currency) }}',
                        message: '{{ __('app.delete_confirm_msg') }}'
                    })"
                    class="btn-danger btn-sm flex-shrink-0">
                <i class="fa-solid fa-trash"></i>
                {{ __('app.delete') }}
            </button>
        </div>
    </div>
    @endif

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('settings.currencies.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
