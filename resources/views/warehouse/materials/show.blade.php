@extends('layouts.app')

@section('title', __('warehouse.material_details'))
@section('breadcrumb', $material->localized_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $material->localized_name }}</h1>
        <p class="page-subtitle">{{ __('warehouse.material_details') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('warehouse.materials.edit', $material) }}" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            {{ __('app.edit') }}
        </a>
        <a href="{{ route('warehouse.materials.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            {{ __('app.back_to_list') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-4">{{ __('warehouse.material_details') }}</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_code') }}</dt>
                <dd class="font-bold text-slate-800 font-mono">{{ $material->code }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_category') }}</dt>
                <dd class="font-bold text-slate-800">{{ $material->category?->localized_name }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_unit') }}</dt>
                <dd class="font-bold text-slate-800">{{ $material->unit?->localized_name }}</dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.min_stock_level') }}</dt>
                <dd class="font-bold text-slate-800">{{ $material->min_stock_level ?? '—' }}</dd>
            </div>
            @if($material->description)
            <div class="sm:col-span-2">
                <dt class="text-slate-400 font-medium mb-0.5">{{ __('warehouse.material_description') }}</dt>
                <dd class="text-slate-700">{{ $material->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-1">{{ __('warehouse.total_stock') }}</h3>
        <p class="text-3xl font-black text-emerald-600 mb-4">{{ number_format($material->stocks->sum('quantity'), 3) }}</p>

        <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2">{{ __('warehouse.stock_by_warehouse') }}</h4>
        @if($material->stocks->isEmpty())
            <p class="text-sm text-slate-400">—</p>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($material->stocks as $stock)
                    <li class="flex items-center justify-between py-2 text-sm">
                        <span class="text-slate-600">{{ $stock->warehouse?->localized_name }}</span>
                        <span class="font-bold text-slate-800">{{ number_format($stock->quantity, 3) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
