@extends('layouts.app')

@section('title', __('warehouse.categories_list'))
@section('breadcrumb', __('warehouse.categories'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.categories_list') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.categories_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.categories.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('warehouse.add_category') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-layer-group text-emerald-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $categories->count() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('warehouse.total_categories') }}</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    @if($categories->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-layer-group text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.no_categories') }}</p>
            <a href="{{ route('warehouse.categories.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('warehouse.add_first_category') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.category_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.parent_category') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('warehouse.category_code') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($categories as $category)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $category->localized_name }}</p></td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $category->parent?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-600">{{ $category->code ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($category->status)
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
                                <a href="{{ route('warehouse.categories.edit', $category) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('warehouse.categories.destroy', $category) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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
