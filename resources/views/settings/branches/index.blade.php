@extends('layouts.app')

@section('title', __('settings.branches_list'))
@section('breadcrumb', __('settings.branches'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.branches_list') }}</h1>
        <p class="page-subtitle">{{ __('settings.branches_subtitle') }}</p>
    </div>
    <a href="{{ route('settings.branches.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('settings.add_branch') }}
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-code-branch text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $branches->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.total_branches') }}</p>
        </div>
    </div>
    @php $main = $branches->firstWhere('is_main', true); @endphp
    @if($main)
    <div class="card px-5 py-4 flex items-center gap-4 border-amber-200">
        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-star text-amber-600"></i>
        </div>
        <div>
            <p class="text-sm font-black text-slate-800">{{ $main->localized_name }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('settings.main_branch') }}</p>
        </div>
    </div>
    @endif
</div>

<div class="card overflow-hidden">
    @if($branches->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-code-branch text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('settings.no_branches') }}</p>
            <a href="{{ route('settings.branches.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('settings.add_first_branch') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('settings.branch_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('app.phone') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell">{{ __('settings.branch_address') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($branches as $branch)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl {{ $branch->is_main ? 'bg-amber-100' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-{{ $branch->is_main ? 'star' : 'code-branch' }} text-{{ $branch->is_main ? 'amber' : 'slate' }}-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $branch->localized_name }}</p>
                                    @if($branch->is_main)
                                        <span class="badge bg-amber-100 text-amber-700 text-[10px]">{{ __('settings.main_branch') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-600" dir="ltr">{{ $branch->phone ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-sm text-slate-600 line-clamp-1">{{ empty($branch->localized_address_lines) ? '—' : implode(' — ', $branch->localized_address_lines) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($branch->status)
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
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('settings.branches.edit', $branch) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', {
                                            action: '{{ route('settings.branches.destroy', $branch) }}',
                                            message: '{{ __('app.delete_confirm_msg') }}'
                                        })"
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
