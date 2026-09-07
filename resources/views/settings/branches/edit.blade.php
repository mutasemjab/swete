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

<form action="{{ route('settings.branches.update', $branch) }}" method="POST" enctype="multipart/form-data">
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

            <div>
                <label class="form-label">{{ __('app.fax') }}</label>
                <input type="text" name="fax" value="{{ old('fax', $branch->fax) }}" dir="ltr"
                       class="form-input @error('fax') is-invalid @enderror">
                @error('fax')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-indigo-500 text-sm"></i>
                {{ __('settings.branch_address') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('app.address_line1') }}</label>
                <input type="text" name="address_line1" value="{{ old('address_line1', $branch->address_line1) }}"
                       class="form-input @error('address_line1') is-invalid @enderror">
                @error('address_line1')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('app.address_line1_en') }}</label>
                <input type="text" name="address_line1_en" value="{{ old('address_line1_en', $branch->address_line1_en) }}" dir="ltr"
                       class="form-input @error('address_line1_en') is-invalid @enderror">
                @error('address_line1_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.po_box') }}</label>
                <input type="text" name="po_box" value="{{ old('po_box', $branch->po_box) }}" dir="ltr"
                       class="form-input @error('po_box') is-invalid @enderror">
                @error('po_box')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.postal_code') }}</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $branch->postal_code) }}" dir="ltr"
                       class="form-input @error('postal_code') is-invalid @enderror">
                @error('postal_code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.city') }}</label>
                <input type="text" name="city" value="{{ old('city', $branch->city) }}"
                       class="form-input @error('city') is-invalid @enderror">
                @error('city')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.city_en') }}</label>
                <input type="text" name="city_en" value="{{ old('city_en', $branch->city_en) }}" dir="ltr"
                       class="form-input @error('city_en') is-invalid @enderror">
                @error('city_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.country') }}</label>
                <input type="text" name="country" value="{{ old('country', $branch->country) }}"
                       class="form-input @error('country') is-invalid @enderror">
                @error('country')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('app.country_en') }}</label>
                <input type="text" name="country_en" value="{{ old('country_en', $branch->country_en) }}" dir="ltr"
                       class="form-input @error('country_en') is-invalid @enderror">
                @error('country_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-image text-indigo-500 text-sm"></i>
                {{ __('settings.branch_logo') }} / {{ __('settings.branch_logo_secondary') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-xs text-slate-400 mb-4">{{ __('settings.branch_logos_hint') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">{{ __('settings.branch_logo') }}</label>
                    @if($branch->logo_url)
                        <div class="mb-2 flex items-center gap-2">
                            <img src="{{ $branch->logo_url }}" alt="" class="h-12 w-auto rounded border border-slate-200 bg-white p-1">
                            <span class="text-xs text-slate-400">{{ __('settings.current_logo') }}</span>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*"
                           class="form-input @error('logo') is-invalid @enderror">
                    @error('logo')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('settings.branch_logo_secondary') }}</label>
                    @if($branch->logo_secondary_url)
                        <div class="mb-2 flex items-center gap-2">
                            <img src="{{ $branch->logo_secondary_url }}" alt="" class="h-12 w-auto rounded border border-slate-200 bg-white p-1">
                            <span class="text-xs text-slate-400">{{ __('settings.current_logo') }}</span>
                        </div>
                    @endif
                    <input type="file" name="logo_secondary" accept="image/*"
                           class="form-input @error('logo_secondary') is-invalid @enderror">
                    @error('logo_secondary')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-toggle-on text-indigo-500 text-sm"></i>
                {{ __('app.status') }}
            </h3>
        </div>
        <div class="px-6 py-5">
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
