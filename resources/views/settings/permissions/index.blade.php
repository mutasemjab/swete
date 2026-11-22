@extends('layouts.app')

@section('title', __('settings.permissions_list'))
@section('breadcrumb', __('settings.permissions'))

@section('content')
<div class="page-header flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('settings.permissions_list') }}</h1>
        <p class="page-subtitle">{{ __('permissions_ui.subtitle') }}</p>
    </div>
    <span class="badge bg-indigo-100 text-indigo-700 text-sm px-4 py-2">
        {{ $total }} {{ __('settings.total_permissions') }}
    </span>
</div>

<form method="GET" action="{{ route('settings.permissions.index') }}" class="card px-5 py-4 mb-5">
    <label for="permission-role" class="form-label">{{ __('permissions_ui.choose_role') }}</label>
    <div class="flex flex-col sm:flex-row gap-3">
        <select id="permission-role" name="role" class="form-select js-select2 flex-1">
            <option value="">{{ __('permissions_ui.directory') }}</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected($selectedRole?->id === $role->id)>
                    {{ \App\Support\PermissionLabels::role($role->name) }}{{ $role->users->isNotEmpty() ? ' — '.$role->users->pluck('name')->implode('، ') : '' }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">{{ __('permissions_ui.show_role') }}</button>
    </div>
</form>

@if($selectedUser && !$selectedRole)
    <p class="rounded-xl bg-amber-50 px-5 py-4 mb-5 text-sm text-amber-800">{{ __('permissions_ui.no_role_user', ['name' => $selectedUser->name]) }}</p>
@endif

@if($selectedRole)
    <div class="card px-5 py-4 mb-5">
        <h2 class="font-bold text-lg text-slate-800">{{ __('permissions_ui.role_access', ['name' => \App\Support\PermissionLabels::role($selectedRole->name)]) }}</h2>
        <p class="text-sm font-semibold text-slate-600 mt-3">{{ __('permissions_ui.affected_users', ['count' => $selectedRole->users->count()]) }}</p>
        <div class="flex flex-wrap gap-2 mt-2">
            @forelse($selectedRole->users as $account)
                <span class="badge bg-indigo-50 text-indigo-700">{{ $account->name }}</span>
            @empty
                <span class="text-sm text-slate-500">{{ __('permissions_ui.no_users') }}</span>
            @endforelse
        </div>
        @if($isSuperAdmin)
            <p class="text-sm text-amber-700 mt-3">{{ __('permissions_ui.super_admin_hint') }}</p>
        @else
            <p class="text-sm text-slate-600 mt-3">{{ __('permissions_ui.role_hint') }}</p>
            <p class="text-sm text-slate-500 mt-2">{{ __('permissions_ui.other_grants_hint') }}</p>
            <p class="text-sm text-slate-500 mt-2">{{ __('permissions_ui.module_hint') }}</p>
            @unless($canEdit)
                <p class="text-sm text-amber-700 mt-2">{{ __('permissions_ui.read_only') }}</p>
            @endunless
        @endif
    </div>
@endif

@if($errors->any())
    <div class="card px-5 py-4 mb-5 text-rose-700" role="alert">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div x-data="{ search: '' }">
    <div class="mb-5">
        <label for="permission-search" class="form-label">{{ __('permissions_ui.search') }}</label>
        <input id="permission-search" type="search" x-model="search" class="form-input max-w-xl"
               placeholder="{{ __('permissions_ui.search') }}" autocomplete="off">
    </div>

    @if($selectedRole)
    <form action="{{ route('settings.permissions.roles.update', $selectedRole) }}" method="POST">
        @csrf
        @method('PUT')
    @endif

    <div class="space-y-5">
        @forelse($permissions as $group => $groupPerms)
        @php
            $groupLabel = \App\Support\PermissionLabels::group($group);
            $groupSearch = mb_strtolower($groupLabel.' '.$groupPerms->map(fn ($permission) => \App\Support\PermissionLabels::permission($permission->name).' '.$permission->name)->implode(' '));
        @endphp
        <section class="card overflow-hidden" data-permission-group data-search="{{ $groupSearch }}"
                 x-show="!search || $el.dataset.search.includes(search.trim().toLocaleLowerCase())">
            <div class="card-header bg-slate-50/80 flex-wrap gap-3">
                <div>
                    <h3 class="font-black text-slate-800">{{ $groupLabel }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $groupPerms->count() }} {{ __('settings.permissions') }}</p>
                </div>
                @if($canEdit)
                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" class="rounded border-slate-300 text-indigo-600"
                               @change="$el.closest('[data-permission-group]').querySelectorAll('input[name=&quot;permissions[]&quot;]').forEach(input => input.checked = $el.checked)">
                        {{ __('permissions_ui.select_group') }}
                    </label>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50/50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3 text-start text-xs font-bold text-slate-500">{{ __('settings.permission_name') }}</th>
                            @unless($selectedRole)
                                <th class="px-5 py-3 text-start text-xs font-bold text-slate-500">{{ __('settings.roles_with_perm') }}</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($groupPerms as $permission)
                        <tr class="hover:bg-slate-50/50"
                            data-search="{{ mb_strtolower($groupLabel.' '.\App\Support\PermissionLabels::permission($permission->name).' '.$permission->name) }}"
                            x-show="!search || $el.dataset.search.includes(search.trim().toLocaleLowerCase())">
                            <td class="px-5 py-3.5">
                                @if($selectedRole)
                                    <label class="flex items-center gap-3 {{ $canEdit ? 'cursor-pointer' : '' }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               @checked(in_array($permission->name, session()->hasOldInput() ? old('permissions', []) : $selectedPermissions, true))
                                               @disabled(!$canEdit)
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        @include('settings.permissions._label')
                                    </label>
                                @else
                                    @include('settings.permissions._label')
                                @endif
                            </td>
                            @unless($selectedRole)
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($permission->roles as $role)
                                            @can('settings.roles.view')
                                                <a href="{{ route('settings.roles.show', $role) }}" class="badge bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                                    {{ \App\Support\PermissionLabels::role($role->name) }}
                                                </a>
                                            @else
                                                <span class="badge bg-indigo-50 text-indigo-700">{{ \App\Support\PermissionLabels::role($role->name) }}</span>
                                            @endcan
                                        @empty
                                            <span class="text-xs text-slate-400">—</span>
                                        @endforelse
                                    </div>
                                </td>
                            @endunless
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @empty
            <div class="card p-8 text-center text-slate-500">{{ __('settings.no_permissions') }}</div>
        @endforelse
        <p x-cloak x-show="search && !Array.from($el.parentElement.querySelectorAll('[data-permission-group]')).some(group => group.dataset.search.includes(search.trim().toLocaleLowerCase()))"
           class="text-center text-slate-500 py-5">{{ __('permissions_ui.no_matches') }}</p>
    </div>

    @if($canEdit)
        <div class="sticky bottom-4 mt-5 flex flex-wrap items-center gap-3 bg-white border border-slate-200 shadow-lg rounded-2xl px-5 py-4">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk"></i>
                {{ __('permissions_ui.save') }}
            </button>
            <a href="{{ route('settings.permissions.index', ['role' => $selectedRole->id]) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
            <span class="text-sm text-slate-500">{{ \App\Support\PermissionLabels::role($selectedRole->name) }}</span>
        </div>
    @endif
    @if($selectedRole)
    </form>
    @endif
</div>
@endsection
