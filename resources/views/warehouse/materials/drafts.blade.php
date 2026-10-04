@extends('layouts.app')

@section('title', __('warehouse.draft_materials'))
@section('breadcrumb', __('warehouse.draft_materials'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.draft_materials') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.draft_materials_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.materials.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<div class="card overflow-hidden" x-data="{
        selected: [],
        toggleAll(checked) { this.selected = checked ? {{ $materials->pluck('id')->values()->toJson() }} : []; },
     }">
    @if($materials->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-circle-check text-emerald-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">{{ __('warehouse.no_draft_materials') }}</p>
        </div>
    @else
        <form action="{{ route('warehouse.materials.bulk-destroy') }}" method="POST"
              @submit="if (! confirm('{{ __('app.delete_confirm_msg') }}')) $event.preventDefault()">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="material_ids[]" :value="id">
            </template>

            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between" x-show="selected.length > 0" x-cloak>
                <p class="text-sm font-semibold text-slate-600" x-text="selected.length + ' {{ __('app.selected') }}'"></p>
                <button type="submit" class="btn-danger btn-sm">
                    <i class="fa-solid fa-trash"></i>
                    {{ __('warehouse.delete_selected_drafts') }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 w-10">
                                <input type="checkbox" @change="toggleAll($event.target.checked)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material_code') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material_name') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ __('warehouse.material_category') }}</th>
                            <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('app.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($materials as $material)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <input type="checkbox" value="{{ $material->id }}" x-model.number="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg text-sm">{{ $material->code }}</span>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-800">{{ $material->localized_name }}</td>
                            <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">{{ $material->category?->localized_name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-500" dir="ltr">{{ $material->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    @endif
</div>
@endsection
