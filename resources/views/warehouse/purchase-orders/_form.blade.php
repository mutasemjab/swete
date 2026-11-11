@php
    $order = $order ?? null;
    $seedItems = collect(
        old('items')
        ?? ($order?->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'quantity'    => (float) $i->quantity,
                'unit_price'  => (float) $i->unit_price,
                'notes'       => $i->notes,
            ])->values())
        ?? [['material_id' => '', 'quantity' => '', 'unit_price' => '', 'notes' => '']]
    )->values();
@endphp

<div x-data="{
       items: {{ $seedItems->toJson() }},
       addItem() { this.items.push({ material_id: '', quantity: '', unit_price: '', notes: '' }); this.$nextTick(() => window.initSelect2()); },
       removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
       lineTotal(item) { return (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0); },
       get total() { return this.items.reduce((sum, i) => sum + this.lineTotal(i), 0); },
       fmt(n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 }); },
     }"
     x-init="$nextTick(() => window.initSelect2())">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-emerald-500 text-sm"></i>
                {{ __('warehouse.purchase_orders_list') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('warehouse.purchase_order_supplier') }} <span class="text-rose-500">*</span></label>
                <select name="supplier_id" class="js-select2 form-select @error('supplier_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $order?->supplier_id) == $supplier->id)>{{ $supplier->localized_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('warehouse.purchase_order_warehouse') }} <span class="text-rose-500">*</span></label>
                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id', $order?->warehouse_id) == $warehouse->id)>{{ $warehouse->localized_name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('warehouse.purchase_order_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', $order?->date?->toDateString() ?? now()->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('warehouse.purchase_order_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $order?->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-emerald-500 text-sm"></i>
                {{ __('warehouse.purchase_items') }}
            </h3>
            <div class="flex items-center gap-2">
                @include('components.material-quick-add-modal', [
                    'targetSelector' => 'select[name$="[material_id]"]',
                    'categories'     => $materialCategories,
                    'units'          => $units,
                    'label'          => __('warehouse.add_material_quick'),
                ])
                <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('warehouse.purchase_add_item') }}
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_material') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('warehouse.purchase_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-36">{{ __('warehouse.purchase_item_unit_price') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('warehouse.purchase_item_total') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.purchase_item_notes') }}</th>
                        <th class="px-5 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-5 py-2.5">
                                <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="js-select2 form-select" required>
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
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                       step="0.0001" min="0" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5 text-slate-700 font-bold" dir="ltr" x-text="fmt(lineTotal(item))"></td>
                            <td class="px-5 py-2.5">
                                <input type="text" :name="`items[${index}][notes]`" x-model="item.notes" class="form-input">
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <button type="button" @click="removeItem(index)" title="{{ __('warehouse.purchase_remove_item') }}"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/50">
                        <td colspan="3" class="px-5 py-3 text-end font-bold text-slate-600">{{ __('warehouse.purchase_total') }}</td>
                        <td class="px-5 py-3 font-black text-slate-800" dir="ltr" x-text="fmt(total)"></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @error('items')<p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $order ? __('app.save') : __('warehouse.add_purchase_order') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</div>
