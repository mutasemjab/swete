@extends('layouts.app')

@section('title', $role->name)
@section('breadcrumb', $role->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $role->name }}</h1>
        <p class="page-subtitle">{{ __('settings.role_info') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('settings.roles.edit', $role) }}" class="btn-primary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('settings.roles.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <div class="card px-6 py-6">
        <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center mb-4">
            <i class="fa-solid fa-shield-halved text-indigo-600 text-2xl"></i>
        </div>
        <h2 class="font-black text-slate-800 text-xl font-mono">{{ $role->name }}</h2>
        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-4">
            <div>
                <p class="text-2xl font-black text-slate-800">{{ $role->permissions->count() }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('settings.permissions_count') }}</p>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-800">{{ $role->users->count() }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ __('settings.users_with_role') }}</p>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-4">{{ __('app.created_at') }}: {{ $role->created_at->format('Y/m/d') }}</p>
    </div>

    <div class="lg:col-span-2 space-y-5">

        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-key text-indigo-500 text-sm"></i>
                    {{ __('settings.role_permissions') }}
                </h3>
                <span class="badge bg-emerald-100 text-emerald-700">{{ $role->permissions->count() }}</span>
            </div>
            <div class="px-6 py-5">
                @if($role->permissions->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-4">{{ __('settings.no_permissions') }}</p>
                @else
                    @foreach($role->permissions->groupBy(fn($p) => explode('.', $p->name)[0]) as $group => $perms)
                    <div class="mb-4 last:mb-0">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">{{ $group }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($perms as $perm)
                            <span class="badge bg-indigo-50 text-indigo-700 font-mono text-[11px]">{{ $perm->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        @if($role->users->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-500 text-sm"></i>
                    {{ __('settings.users_with_role') }}
                </h3>
                <span class="badge bg-slate-100 text-slate-700">{{ $role->users->count() }}</span>
            </div>
            <div class="px-6 py-4 flex flex-wrap gap-3">
                @foreach($role->users as $u)
                <a href="{{ route('settings.users.show', $u) }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br {{ $u->avatar_gradient }}
                                flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                        {{ $u->initials }}
                    </div>
                    <span class="text-sm font-semibold text-slate-700">{{ $u->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
