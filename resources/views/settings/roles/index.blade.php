@extends('layouts.app')

@section('title', __('settings.roles_list'))
@section('breadcrumb', __('settings.roles'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.roles_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.roles_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.roles.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('settings.add_role') }}
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-shield-halved text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $roles->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.total_roles') }}</p>
        </div>
    </div>
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-key text-emerald-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $roles->sum('permissions_count') }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.total_permissions') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    @if($roles->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-shield-halved text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('settings.no_roles') }}</p>
            <a href="{{ route('settings.roles.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('settings.add_first_role') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.role_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.permissions_count') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.users_with_role') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('app.created_at') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($roles as $role)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-shield-halved text-indigo-600 text-sm"></i>
                                </div>
                                <span class="font-mono font-bold text-slate-800">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-key text-[10px]"></i>
                                {{ $role->permissions_count }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-users text-[10px]"></i>
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-500">{{ $role->created_at->format('Y/m/d') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('settings.roles.show', $role) }}"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="{{ __('app.view') }}">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('settings.roles.edit', $role) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                @if($role->users_count === 0)
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', {
                                            action: '{{ route('settings.roles.destroy', $role) }}',
                                            message: '{{ __('app.delete_confirm_msg') }}'
                                        })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                                @else
                                <div class="p-1.5 text-slate-300 !cursor-not-allowed" title="{{ __('settings.cannot_delete_role') }}">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
