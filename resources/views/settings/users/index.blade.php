@extends('layouts.app')

@section('title', __('settings.users_list'))
@section('breadcrumb', __('settings.users'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.users_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.users_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.users.create') }}" class="btn-primary">
        <i class="fa-solid fa-user-plus"></i>
        {{ __('settings.add_user') }}
    </a>
</div>

{{-- Stats row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => __('settings.total_users'),  'value' => $totalUsers,         'icon' => 'users',         'color' => 'indigo'],
        ['label' => __('settings.active_users'),  'value' => $activeUsers,        'icon' => 'user-check',    'color' => 'emerald'],
        ['label' => __('settings.inactive_users'),'value' => $totalUsers - $activeUsers, 'icon' => 'user-xmark', 'color' => 'slate'],
        ['label' => __('settings.admins_count'),  'value' => $admins,             'icon' => 'user-shield',   'color' => 'amber'],
    ] as $stat)
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-{{ $stat['color'] }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $stat['value'] }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $stat['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('settings.users.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <select name="role" class="form-select w-44">
        <option value="">{{ __('settings.all_roles') }}</option>
        @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
        @endforeach
    </select>
    <select name="status" class="form-select w-44">
        <option value="">{{ __('app.all_statuses') }}</option>
        <option value="1" @selected(request('status') === '1')>{{ __('app.active') }}</option>
        <option value="0" @selected(request('status') === '0')>{{ __('app.inactive') }}</option>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('settings.users.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

{{-- Table --}}
<div class="card overflow-hidden">
    @if($users->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-users text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                {{ request()->hasAny(['search','role','status']) ? __('settings.no_users_search') : __('settings.no_users') }}
            </p>
            @if(!request()->hasAny(['search','role','status']))
                <a href="{{ route('settings.users.create') }}" class="btn-primary mt-5">
                    <i class="fa-solid fa-user-plus"></i>
                    {{ __('settings.add_first_user') }}
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.user') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('app.phone') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.assign_role') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell">{{ __('settings.last_login') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $user->avatar_gradient }}
                                            flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                                    {{ $user->initials }}
                                </div>
                                <div>
                                    <a href="{{ route('settings.users.show', $user) }}"
                                       class="font-bold text-slate-800 hover:text-indigo-600 transition-colors">
                                        {{ $user->name }}
                                    </a>
                                    <p class="text-xs text-slate-400 mt-0.5" dir="ltr">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-600" dir="ltr">{{ $user->phone ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($user->roles->isNotEmpty())
                                <span class="badge bg-indigo-100 text-indigo-700">
                                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                    {{ $user->roles->first()->name }}
                                </span>
                            @else
                                <span class="text-slate-400 text-sm">{{ __('settings.no_role') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($user->status)
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    {{ __('app.active') }}
                                </span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    {{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-sm text-slate-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('settings.never_logged') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('settings.users.show', $user) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('settings.users.edit', $user) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', {
                                            action: '{{ route('settings.users.destroy', $user) }}',
                                            message: '{{ __('settings.delete_user_confirm') }} {{ $user->name }}؟'
                                        })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $users->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $users->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $users->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($users->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
