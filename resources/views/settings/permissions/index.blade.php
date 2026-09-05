@extends('layouts.app')

@section('title', __('settings.permissions_list'))
@section('breadcrumb', __('settings.permissions'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.permissions_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.permissions_subtitle') }}</p>
    </div>
    <div class="badge bg-indigo-100 text-indigo-700 text-sm px-4 py-2">
        <i class="fa-solid fa-key"></i>
        {{ $total }} {{ __('settings.total_permissions') }}
    </div>
</div>

@if($permissions->isEmpty())
    <div class="card flex flex-col items-center justify-center py-20 text-center">
        <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
            <i class="fa-solid fa-key text-slate-400 text-3xl"></i>
        </div>
        <p class="text-slate-800 font-bold text-lg">{{ __('settings.no_permissions') }}</p>
        <p class="text-slate-400 text-sm mt-1">{{ __('settings.permissions_subtitle') }}</p>
    </div>
@else
    <div class="space-y-5">
        @foreach($permissions as $group => $groupPerms)
        <div class="card overflow-hidden">
            <div class="card-header bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fa-solid fa-folder text-indigo-600 text-xs"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-700 uppercase tracking-wider text-sm">{{ $group }}</h3>
                        <p class="text-xs text-slate-400">{{ $groupPerms->count() }} {{ __('settings.permissions') }}</p>
                    </div>
                </div>
                <span class="badge bg-indigo-100 text-indigo-700">{{ $groupPerms->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50/50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.permission_name') }}</th>
                            <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.roles_with_perm') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($groupPerms as $permission)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-sm text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $permission->name }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($permission->roles as $role)
                                        <a href="{{ route('settings.roles.show', $role) }}"
                                           class="badge bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                            <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                            {{ $role->name }}
                                        </a>
                                    @empty
                                        <span class="text-xs text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
