@extends('layouts.app')

@section('title', __('settings.edit_role'))
@section('breadcrumb', __('settings.edit_role'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.edit_role') }}</h1>
        <p class="page-subtitle">{{ __('settings.edit_role_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.roles.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('settings.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-500 text-sm"></i>
                {{ __('settings.role_info') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            <label class="form-label">{{ __('settings.role_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" dir="ltr"
                   class="form-input max-w-xs @error('name') is-invalid @enderror">
            <p class="text-xs text-slate-400 mt-1.5">{{ __('settings.role_name_hint') }}</p>
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-key text-indigo-500 text-sm"></i>
                {{ __('settings.role_permissions') }}
            </h3>
            <p class="text-xs text-slate-400">{{ __('settings.select_permissions') }}</p>
        </div>
        <div class="px-6 py-5 space-y-6">
            @forelse($permissions as $group => $groupPerms)
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest">{{ $group }}</h4>
                    <div class="flex-1 h-px bg-slate-100"></div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-500 hover:text-slate-700" x-data>
                        <input type="checkbox"
                               @change="$el.closest('label').closest('div').nextElementSibling.querySelectorAll('input[type=checkbox]').forEach(c => c.checked = $el.checked)"
                               class="rounded border-slate-300 text-indigo-600">
                        {{ __('settings.select_permissions') }}
                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($groupPerms as $permission)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-slate-200
                                  hover:border-indigo-300 hover:bg-indigo-50/50 cursor-pointer transition-all
                                  has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                               @checked(in_array($permission->name, old('permissions', $rolePermissions)))
                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-mono text-slate-700">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-8">{{ __('settings.no_permissions') }}</p>
            @endforelse
        </div>
    </div>

    @if($role->users->isEmpty())
    <div class="card mb-5 border-rose-200">
        <div class="card-header border-rose-100">
            <h3 class="font-bold text-rose-700 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                {{ __('app.danger_zone') }}
            </h3>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700">{{ __('settings.role_deleted') }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('app.irreversible') }}</p>
            </div>
            <button type="button"
                    @click="$dispatch('delete-confirm', {
                        action: '{{ route('settings.roles.destroy', $role) }}',
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
        <a href="{{ route('settings.roles.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
