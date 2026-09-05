@extends('layouts.app')

@section('title', __('warehouse.report_materials'))
@section('breadcrumb', __('warehouse.report_materials'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.report_materials') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.report_materials_subtitle') }}</p>
    </div>
</div>

<form method="GET" action="{{ route('warehouse.reports.materials') }}" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="warehouse_id" class="form-select w-52">
        <option value="">{{ __('warehouse.report_all_warehouses') }}</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>{{ $warehouse->localized_name }}</option>
        @endforeach
    </select>
    <select name="category_id" class="form-select w-52">
        <option value="">{{ __('warehouse.all_categories') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->localized_name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        {{ __('warehouse.report_filter') }}
    </button>
    @if(request()->hasAny(['warehouse_id','category_id']))
        <a href="{{ route('warehouse.reports.materials') }}" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            {{ __('app.clear_filters') }}
        </a>
    @endif
</form>

<div class="card overflow-hidden">
    @if($stocks->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-chart-column text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.report_no_data') }}</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('warehouse.material_category') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.warehouse') }}</th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_quantity') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stocks as $stock)
                    @php $low = $stock->material->min_stock_level !== null && $stock->quantity < $stock->material->min_stock_level; @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800">{{ $stock->material->localized_name }}</span>
                            <span class="text-xs text-slate-400 ms-1 font-mono">{{ $stock->material->code }}</span>
                            @if($low)
                                <span class="badge bg-rose-100 text-rose-700 text-[10px] ms-1">{{ __('warehouse.report_low_stock') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $stock->material->category?->localized_name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $stock->warehouse?->localized_name }}</td>
                        <td class="px-5 py-4 font-bold text-slate-800">
                            {{ number_format($stock->quantity, 3) }}
                            <span class="text-xs text-slate-400 font-normal">{{ $stock->material->unit?->symbol }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
