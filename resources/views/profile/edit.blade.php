@extends('layouts.app')

@section('title', __('app.profile'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('app.profile') }}</h1>
        <p class="page-subtitle">{{ __('profile.subtitle') }}</p>
    </div>
</div>

<form action="{{ route('profile.update') }}" method="POST" class="max-w-3xl">
    @csrf
    @method('PATCH')

    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-bold text-slate-700">{{ __('settings.basic_info') }}</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label for="name" class="form-label">{{ __('app.name') }} <span class="text-rose-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                       required maxlength="100" autocomplete="name"
                       class="form-input @error('name') is-invalid @enderror">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="form-label">{{ __('app.email') }} <span class="text-rose-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                       required maxlength="255" autocomplete="email" dir="ltr"
                       class="form-input @error('email') is-invalid @enderror">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="form-label">{{ __('app.phone') }}</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       maxlength="30" autocomplete="tel" dir="ltr"
                       class="form-input @error('phone') is-invalid @enderror">
                @error('phone')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h2 class="font-bold text-slate-700">{{ __('app.change_password') }}</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <p class="sm:col-span-2 text-sm text-slate-500">{{ __('profile.password_hint') }}</p>
            <div class="sm:col-span-2">
                <label for="current_password" class="form-label">{{ __('profile.current_password') }}</label>
                <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                       class="form-input @error('current_password') is-invalid @enderror">
                @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="form-label">{{ __('profile.new_password') }}</label>
                <input id="password" type="password" name="password" autocomplete="new-password" minlength="8"
                       placeholder="{{ __('app.password_min') }}"
                       class="form-input @error('password') is-invalid @enderror">
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="form-label">{{ __('app.password_confirm') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                       class="form-input">
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('dashboard') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
