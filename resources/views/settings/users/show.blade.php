@extends('layouts.app')

@section('title', $user->name)
@section('breadcrumb', $user->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.user_details') }}</h1>
        <p class="page-subtitle">{{ __('settings.user_details_subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('settings.users.edit', $user) }}" class="btn-primary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('settings.users.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Profile card --}}
    <div class="card overflow-hidden">
        <div class="h-24 bg-gradient-to-br {{ $user->avatar_gradient }}"></div>
        <div class="px-6 pb-6 -mt-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br {{ $user->avatar_gradient }}
                        flex items-center justify-center text-white text-2xl font-black
                        mx-auto border-4 border-white shadow-lg mb-4">
                {{ $user->initials }}
            </div>
            <h2 class="font-black text-slate-800 text-xl">{{ $user->name }}</h2>
            <p class="text-slate-400 text-sm mt-1" dir="ltr">{{ $user->email }}</p>

            @if($user->roles->isNotEmpty())
            <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-bold">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                {{ $user->roles->first()->name }}
            </span>
            @endif

            <div class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-black text-slate-800">
                        {{ $user->created_at->diffInDays(now()) }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ __('settings.days_in_system') }}</p>
                </div>
                <div class="text-center">
                    @if($user->status)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            {{ __('app.active') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 text-slate-500 rounded-xl text-xs font-bold">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                            {{ __('app.inactive') }}
                        </span>
                    @endif
                    <p class="text-xs text-slate-400 mt-1">{{ __('settings.account_status') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Contact / Account info --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500 text-sm"></i>
                    {{ __('settings.basic_info') }}
                </h3>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('app.name') }}</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('app.email') }}</p>
                    <p class="text-sm font-semibold text-slate-800" dir="ltr">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('app.phone') }}</p>
                    <p class="text-sm font-semibold text-slate-800" dir="ltr">{{ $user->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('settings.identifier') }}</p>
                    <p class="text-sm font-mono font-semibold text-slate-800">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('app.created_at') }}</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $user->created_at->format('Y/m/d') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('settings.last_login') }}</p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('settings.never_logged') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        @if($user->roles->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-key text-indigo-500 text-sm"></i>
                    {{ __('settings.permissions_section') }}
                </h3>
                <span class="badge bg-indigo-100 text-indigo-700">{{ $user->roles->first()->name }}</span>
            </div>
            <div class="px-6 py-5">
                @php $perms = $user->roles->first()->permissions; @endphp
                @if($perms->isEmpty())
                    <p class="text-sm text-slate-400">{{ __('settings.no_permissions') }}</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($perms as $perm)
                            <span class="badge bg-slate-100 text-slate-600 font-mono text-[11px]">{{ $perm->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
