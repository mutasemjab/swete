@extends('layouts.app')

@section('title', __('warehouse.add_material_request'))
@section('breadcrumb', __('warehouse.add_material_request'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_material_request') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_material_request_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.material-requests.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('warehouse.material-requests.store') }}" method="POST"
      x-data="{
        items: [{ material_id: '', quantity: '', notes: '' }],
        addItem() { this.items.push({ material_id: '', quantity: '', notes: '' }); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
      }">
    @csrf

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-emerald-500 text-sm"></i>
                {{ __('warehouse.material_request') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('warehouse.material_request_warehouse') }} <span class="text-rose-500">*</span></label>
                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->localized_name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('warehouse.material_request_needed_by') }}</label>
                <input type="date" name="needed_by_date" value="{{ old('needed_by_date') }}"
                       class="form-input @error('needed_by_date') is-invalid @enderror">
                @error('needed_by_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('warehouse.material_request_reason') }}</label>
                <textarea name="reason" rows="2" class="form-input @error('reason') is-invalid @enderror">{{ old('reason') }}</textarea>
                @error('reason')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-emerald-500 text-sm"></i>
                {{ __('warehouse.voucher_items') }}
            </h3>
            <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                {{ __('warehouse.voucher_add_item') }}
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_material') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('warehouse.voucher_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.voucher_item_notes') }}</th>
                        <th class="px-5 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-5 py-2.5">
                                <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="form-select" required>
                                    <option value="">{{ __('app.select') }}</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->localized_name }} ({{ $material->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="text" :name="`items[${index}][notes]`" x-model="item.notes" class="form-input">
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <button type="button" @click="removeItem(index)" title="{{ __('warehouse.voucher_remove_item') }}"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        @error('items')<p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_material_request') }}
        </button>
        <a href="{{ route('warehouse.material-requests.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
