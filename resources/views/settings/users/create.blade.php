@extends('layouts.app')

@section('title', __('settings.add_user'))
@section('breadcrumb', __('settings.add_user'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.add_user') }}</h1>
        <p class="page-subtitle">{{ __('settings.add_user_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.users.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('settings.users.store') }}" method="POST">
    @csrf

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-user text-indigo-500 text-sm"></i>
                {{ __('settings.basic_info') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('app.name') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-input @error('name') is-invalid @enderror"
                       placeholder="{{ __('app.name') }}">
                @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.email') }} <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" dir="ltr"
                       class="form-input @error('email') is-invalid @enderror"
                       placeholder="email@example.com">
                @error('email')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr"
                       class="form-input @error('phone') is-invalid @enderror"
                       placeholder="+962 7X XXX XXXX">
                @error('phone')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.password') }} <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" name="password"
                           class="form-input pe-10 @error('password') is-invalid @enderror"
                           placeholder="{{ __('app.password_min') }}">
                    <button type="button" @click="show = !show"
                            class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                        <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                    </button>
                </div>
                @error('password')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.password_confirm') }} <span class="text-rose-500">*</span></label>
                <input type="password" name="password_confirmation"
                       class="form-input"
                       placeholder="{{ __('app.password_confirm') }}">
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-500 text-sm"></i>
                {{ __('settings.permissions_section') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="form-label">{{ __('settings.assign_role') }}</label>
                <select name="role" class="form-select">
                    <option value="">{{ __('settings.no_role') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1.5">{{ __('settings.login_tip') }}</p>
            </div>

            <div class="flex items-center gap-4 pt-7">
                <label class="relative inline-flex items-center cursor-pointer" x-data dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           @checked(old('status', '1') == '1')>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('settings.add_user') }}
        </button>
        <a href="{{ route('settings.users.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
