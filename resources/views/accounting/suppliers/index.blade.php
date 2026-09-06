@extends('layouts.app')

@section('title', __('accounting.suppliers_list'))
@section('breadcrumb', __('accounting.suppliers'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.suppliers_list') }}</h1>
        <p class="page-subtitle">{{ __('accounting.suppliers_subtitle') }}</p>
    </div>
    <a href="{{ route('accounting.suppliers.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        {{ __('accounting.add_supplier') }}
    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-truck text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800">{{ $suppliers->total() }}</p>
            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ __('accounting.total_suppliers') }}</p>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('accounting.suppliers.index') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('app.search') }}..."
                   class="form-input ps-10">
        </div>
    </div>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('app.search') }}
    </button>
    @if(request()->hasAny(['search']))
        <a href="{{ route('accounting.suppliers.index') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($suppliers->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-truck text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('accounting.no_suppliers') }}</p>
            <a href="{{ route('accounting.suppliers.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                {{ __('accounting.add_first_supplier') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.party_code') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.party_name') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('accounting.party_group') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell">{{ __('accounting.party_phone') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($suppliers as $supplier)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $supplier->code }}</span>
                        </td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $supplier->localized_name }}</p></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $supplier->group?->localized_name ?? '—' }}</td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600" dir="ltr">{{ $supplier->phone ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if($supplier->status)
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
                                <a href="{{ route('accounting.suppliers.edit', $supplier) }}"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('app.edit') }}">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="{{ __('app.delete') }}"
                                        @click="$dispatch('delete-confirm', { action: '{{ route('accounting.suppliers.destroy', $supplier) }}', message: '{{ __('app.delete_confirm_msg') }}' })"
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

        @if($suppliers->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                {{ __('app.showing') }} <span class="font-bold text-slate-700">{{ $suppliers->firstItem() }}</span>
                {{ __('app.to') }} <span class="font-bold text-slate-700">{{ $suppliers->lastItem() }}</span>
                {{ __('app.of') }} <span class="font-bold text-slate-700">{{ $suppliers->total() }}</span>
                {{ __('app.results') }}
            </p>
            <div class="flex gap-1">
                @if($suppliers->onFirstPage())
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.previous') }}</span>
                @else
                    <a href="{{ $suppliers->previousPageUrl() }}" class="btn-secondary btn-sm">{{ __('app.previous') }}</a>
                @endif
                @if($suppliers->hasMorePages())
                    <a href="{{ $suppliers->nextPageUrl() }}" class="btn-primary btn-sm">{{ __('app.next') }}</a>
                @else
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed">{{ __('app.next') }}</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
