@extends('layouts.app')

@section('title', __('settings.edit_branch'))
@section('breadcrumb', __('settings.edit_branch'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.edit_branch') }}</h1>
        <p class="page-subtitle">{{ $branch->localized_name }}</p>
    </div>
    <a href="{{ route('settings.branches.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('settings.branches.update', $branch) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-code-branch text-indigo-500 text-sm"></i>
                {{ __('settings.branch_name') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="form-label">{{ __('app.name') }} <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $branch->name) }}"
                       class="form-input @error('name') is-invalid @enderror">
                @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.name_en') }}</label>
                <input type="text" name="name_en" value="{{ old('name_en', $branch->name_en) }}" dir="ltr"
                       class="form-input @error('name_en') is-invalid @enderror">
                @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('settings.branch_phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" dir="ltr"
                       class="form-input @error('phone') is-invalid @enderror">
                @error('phone')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('settings.branch_address') }}</label>
                <input type="text" name="address" value="{{ old('address', $branch->address) }}"
                       class="form-input @error('address') is-invalid @enderror">
                @error('address')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('app.address_en') }}</label>
                <input type="text" name="address_en" value="{{ old('address_en', $branch->address_en) }}" dir="ltr"
                       class="form-input @error('address_en') is-invalid @enderror">
                @error('address_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-6">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="is_main" value="0">
                    <input type="checkbox" name="is_main" value="1" class="sr-only peer"
                           @checked(old('is_main', $branch->is_main))>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-amber-400 rounded-full peer
                                peer-checked:bg-amber-500 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('settings.is_main') }}</span>
                </label>

                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           @checked(old('status', $branch->status))>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
                </label>
            </div>
            <p class="text-xs text-slate-400 sm:col-span-2">{{ __('settings.is_main_hint') }}</p>
        </div>
    </div>

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
                        action: '{{ route('settings.branches.destroy', $branch) }}',
                        message: '{{ __('app.delete_confirm_msg') }}'
                    })"
                    class="btn-danger btn-sm flex-shrink-0">
                <i class="fa-solid fa-trash"></i>
                {{ __('app.delete') }}
            </button>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('settings.branches.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
