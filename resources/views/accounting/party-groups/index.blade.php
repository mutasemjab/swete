@extends('layouts.app')

@section('title', __('accounting.' . $type . '_groups'))
@section('breadcrumb', __('accounting.' . $type . '_groups'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.' . $type . '_groups') }}</h1>
        <p class="page-subtitle">{{ __('accounting.group_name') }}</p>
    </div>
    <a href="{{ route("accounting.{$type}-groups.create") }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('accounting.add_group_' . $type) }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-sitemap text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $groups->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('accounting.total_groups') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    @if($groups->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-sitemap text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('accounting.no_groups') }}</p>
            <a href="{{ route("accounting.{$type}-groups.create") }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('accounting.add_first_group') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.group_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.parent_group') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($groups as $group)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $group->localized_name }}</p></td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $group->parent?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if($group->status)
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('app.active') }}
                                </span>
                            @else
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>{{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route("accounting.{$type}-groups.edit", $group) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route("accounting.{$type}-groups.destroy", $group) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
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
