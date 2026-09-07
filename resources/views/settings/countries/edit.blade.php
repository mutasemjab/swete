@extends('layouts.app')

@section('title', __('settings.edit_country'))
@section('breadcrumb', __('settings.edit_country'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.edit_country') }}</h1>
        <p class="page-subtitle">{{ $country->localized_name }}</p>
    </div>
    <a href="{{ route('settings.countries.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('settings.countries.update', $country) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card mb-5">
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('settings.country_name') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $country->name) }}"
                       class="form-input @error('name') is-invalid @enderror">
                @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">{{ __('app.name_en') }}</label>
                <input type="text" name="name_en" value="{{ old('name_en', $country->name_en) }}" dir="ltr"
                       class="form-input @error('name_en') is-invalid @enderror">
                @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer" @checked(old('status', $country->status))>
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
    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('settings.countries.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
