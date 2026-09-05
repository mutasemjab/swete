@extends('layouts.app')

@section('title', __('settings.edit_user'))
@section('breadcrumb', __('settings.edit_user'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.edit_user') }}</h1>
        <p class="page-subtitle">{{ __('settings.edit_user_subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('settings.users.show', $user) }}" class="btn-secondary">
            <i class="fa-solid fa-eye"></i>
            {{ __('app.view') }}
        </a>
        <a href="{{ route('settings.users.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<form action="{{ route('settings.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

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
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="form-input @error('name') is-invalid @enderror">
                @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.email') }} <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr"
                       class="form-input @error('email') is-invalid @enderror">
                @error('email')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr"
                       class="form-input @error('phone') is-invalid @enderror">
                @error('phone')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5" x-data="{ changePass: false }">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-lock text-indigo-500 text-sm"></i>
                {{ __('settings.security') }}
            </h3>
            <button type="button" @click="changePass = !changePass"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                <span x-text="changePass ? '{{ __('app.cancel_change') }}' : '{{ __('app.change_password') }}'"></span>
            </button>
        </div>
        <div x-show="changePass" x-transition class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('app.password') }}</label>
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
                <label class="form-label">{{ __('app.password_confirm') }}</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>
        <div x-show="!changePass" class="px-6 py-4">
            <p class="text-sm text-slate-400">{{ __('app.password_min') }}</p>
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
                        <option value="{{ $role->name }}"
                                @selected(old('role', $user->roles->first()?->name) === $role->name)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-4 pt-7">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           @checked(old('status', $user->status))>
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

    @if($user->id !== auth()->id())
    <div class="card mb-5 border-rose-200">
        <div class="card-header border-rose-100">
            <h3 class="font-bold text-rose-700 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                {{ __('app.danger_zone') }}
            </h3>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700">{{ __('settings.delete_user') }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('app.irreversible') }}</p>
            </div>
            <button type="button"
                    @click="$dispatch('delete-confirm', {
                        action: '{{ route('settings.users.destroy', $user) }}',
                        message: '{{ __('settings.delete_user_confirm') }} {{ $user->name }}؟'
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
        <a href="{{ route('settings.users.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
