@extends('layouts.app')

@section('title', __('settings.quote_discount_limit'))
@section('breadcrumb', __('settings.quote_discount_limit'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.quote_discount_limit') }}</h1>
        <p class="page-subtitle">{{ __('settings.quote_discount_limit_subtitle') }}</p>
    </div>
</div>

<form action="{{ route('settings.quote-discount-limit.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-percent text-amber-500 text-sm"></i>
                {{ __('settings.quote_discount_limit') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('settings.max_discount_percent') }}</label>
                <input type="number" name="max_discount_percent" value="{{ old('max_discount_percent', $limit->max_discount_percent) }}"
                       step="0.01" min="0" max="100" dir="ltr" class="form-input @error('max_discount_percent') is-invalid @enderror">
                <p class="text-xs text-slate-400 mt-1.5">{{ __('settings.max_discount_percent_hint') }}</p>
                @error('max_discount_percent')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('settings.max_discount_amount') }}</label>
                <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $limit->max_discount_amount) }}"
                       step="0.001" min="0" dir="ltr" class="form-input @error('max_discount_amount') is-invalid @enderror">
                <p class="text-xs text-slate-400 mt-1.5">{{ __('settings.max_discount_amount_hint') }}</p>
                @error('max_discount_amount')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
    </div>
</form>
@endsection
