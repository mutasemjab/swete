@php $purchaseRequest = $purchaseRequest ?? null; @endphp
<div x-data="{
        linkType: '{{ old('link_type', $project ? 'project' : ($serviceCall ? 'service_call' : 'stock')) }}',
        scope: '{{ old('location_scope', $purchaseRequest?->location_scope ?? 'inside_jordan') }}',
        branches: {{ $branches->map(fn ($b) => ['id' => $b->id, 'addressLines' => $b->localized_address_lines])->values()->toJson() }},
        branchId: '{{ old('branch_id', $purchaseRequest?->branch_id ?? $branches->first()?->id) }}',
        get branchAddressLines() {
            const b = this.branches.find(x => String(x.id) === String(this.branchId));
            return b ? b.addressLines : [];
        },
        suppliers: {{ $suppliers->map(fn ($s) => [
            'id' => $s->id,
            'shipping_address_line1' => $s->shipping_address_line1,
            'shipping_address_line1_en' => $s->shipping_address_line1_en,
            'shipping_po_box' => $s->shipping_po_box,
            'shipping_postal_code' => $s->shipping_postal_code,
            'shipping_city' => $s->shipping_city,
            'shipping_city_en' => $s->shipping_city_en,
            'shipping_country' => $s->shipping_country,
            'shipping_country_en' => $s->shipping_country_en,
        ])->values()->toJson() }},
        supplierId: '{{ old('supplier_id', $purchaseRequest?->supplier_id) }}',
        shipping: {
            address_line1: '{{ old('shipping_address_line1', $purchaseRequest?->shipping_address_line1) }}',
            address_line1_en: '{{ old('shipping_address_line1_en', $purchaseRequest?->shipping_address_line1_en) }}',
            po_box: '{{ old('shipping_po_box', $purchaseRequest?->shipping_po_box) }}',
            postal_code: '{{ old('shipping_postal_code', $purchaseRequest?->shipping_postal_code) }}',
            city: '{{ old('shipping_city', $purchaseRequest?->shipping_city) }}',
            city_en: '{{ old('shipping_city_en', $purchaseRequest?->shipping_city_en) }}',
            country: '{{ old('shipping_country', $purchaseRequest?->shipping_country) }}',
            country_en: '{{ old('shipping_country_en', $purchaseRequest?->shipping_country_en) }}',
        },
        fillShippingFromSupplier() {
            const s = this.suppliers.find(x => String(x.id) === String(this.supplierId));
            if (!s) return;
            this.shipping.address_line1 = s.shipping_address_line1 || '';
            this.shipping.address_line1_en = s.shipping_address_line1_en || '';
            this.shipping.po_box = s.shipping_po_box || '';
            this.shipping.postal_code = s.shipping_postal_code || '';
            this.shipping.city = s.shipping_city || '';
            this.shipping.city_en = s.shipping_city_en || '';
            this.shipping.country = s.shipping_country || '';
            this.shipping.country_en = s.shipping_country_en || '';
        },
        additionalNotes: {{ (
            $purchaseRequest?->additionalNotes->map(fn ($n) => ['label' => $n->label, 'value' => $n->value])->values()
            ?? collect(\App\Models\PurchaseRequest::DEFAULT_NOTE_LABELS)->map(fn ($label) => ['label' => $label, 'value' => ''])
        )->toJson() }},
        items: {{ (
            $purchaseRequest?->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'quantity'    => (float) $i->quantity,
                'ercd'        => $i->ercd,
                'unit_price'  => (float) $i->unit_price,
                'features'    => $i->features->pluck('value')->values()->isNotEmpty() ? $i->features->pluck('value')->values() : [''],
            ])->values()
            ?? collect([['material_id' => '', 'quantity' => '', 'ercd' => '', 'unit_price' => '', 'features' => ['']]])
        )->toJson() }},
        addItem() { this.items.push({ material_id: '', quantity: '', ercd: '', unit_price: '', features: [''] }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-cyan-500 text-sm"></i>
                {{ __('external_purchases.purchase_request') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('external_purchases.request_link_type') }}</label>
                <select x-model="linkType" class="form-select">
                    <option value="project">{{ __('external_purchases.link_type_project') }}</option>
                    <option value="service_call">{{ __('external_purchases.link_type_service_call') }}</option>
                    <option value="stock">{{ __('external_purchases.link_type_stock') }}</option>
                </select>
            </div>

            <div x-show="linkType === 'project'">
                <label class="form-label">{{ __('external_purchases.request_project') }}</label>
                <select name="project_id" class="js-select2 form-select @error('project_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id', $project?->id) == $p->id)>{{ $p->number }} — {{ $p->localized_title }}</option>
                    @endforeach
                </select>
                @error('project_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="linkType === 'service_call'">
                <label class="form-label">{{ __('external_purchases.request_service_call') }}</label>
                <select name="service_call_id" class="js-select2 form-select @error('service_call_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($serviceCalls as $sc)
                        <option value="{{ $sc->id }}" @selected(old('service_call_id', $serviceCall?->id) == $sc->id)>{{ $sc->number }} — {{ $sc->title }}</option>
                    @endforeach
                </select>
                @error('service_call_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', $purchaseRequest?->date?->toDateString() ?? now()->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_supplier') }} <span class="text-rose-500">*</span></label>
                <select name="supplier_id" x-model="supplierId" @change="fillShippingFromSupplier()" class="js-select2 form-select @error('supplier_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseRequest?->supplier_id) == $supplier->id)>{{ $supplier->localized_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_currency') }}</label>
                <select name="currency_id" class="js-select2 form-select @error('currency_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(old('currency_id', $purchaseRequest?->currency_id) == $currency->id)>{{ $currency->localized_name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
                @error('currency_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_branch') }} <span class="text-rose-500">*</span></label>
                <select name="branch_id" x-model="branchId" class="js-select2 form-select @error('branch_id') is-invalid @enderror">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $purchaseRequest?->branch_id ?? $branches->first()?->id) == $branch->id)>{{ $branch->localized_name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_address') }}</label>
                <div class="form-input bg-slate-50 text-slate-600 h-auto py-2.5 leading-6">
                    <template x-for="line in branchAddressLines" :key="line">
                        <p x-text="line"></p>
                    </template>
                    <p x-show="branchAddressLines.length === 0">—</p>
                </div>
            </div>

            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">{{ __('external_purchases.request_shipping_address') }}</label>
                </div>

                <div>
                    <label class="form-label">{{ __('app.address_line1') }}</label>
                    <input type="text" name="shipping_address_line1" x-model="shipping.address_line1"
                           class="form-input @error('shipping_address_line1') is-invalid @enderror">
                    @error('shipping_address_line1')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.address_line1_en') }}</label>
                    <input type="text" name="shipping_address_line1_en" x-model="shipping.address_line1_en" dir="ltr"
                           class="form-input @error('shipping_address_line1_en') is-invalid @enderror">
                    @error('shipping_address_line1_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.po_box') }}</label>
                    <input type="text" name="shipping_po_box" x-model="shipping.po_box" dir="ltr"
                           class="form-input @error('shipping_po_box') is-invalid @enderror">
                    @error('shipping_po_box')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.postal_code') }}</label>
                    <input type="text" name="shipping_postal_code" x-model="shipping.postal_code" dir="ltr"
                           class="form-input @error('shipping_postal_code') is-invalid @enderror">
                    @error('shipping_postal_code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.city') }}</label>
                    <input type="text" name="shipping_city" x-model="shipping.city"
                           class="form-input @error('shipping_city') is-invalid @enderror">
                    @error('shipping_city')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.city_en') }}</label>
                    <input type="text" name="shipping_city_en" x-model="shipping.city_en" dir="ltr"
                           class="form-input @error('shipping_city_en') is-invalid @enderror">
                    @error('shipping_city_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.country') }}</label>
                    <input type="text" name="shipping_country" x-model="shipping.country"
                           class="form-input @error('shipping_country') is-invalid @enderror">
                    @error('shipping_country')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.country_en') }}</label>
                    <input type="text" name="shipping_country_en" x-model="shipping.country_en" dir="ltr"
                           class="form-input @error('shipping_country_en') is-invalid @enderror">
                    @error('shipping_country_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">{{ __('external_purchases.request_location_scope') }}</label>
                    <select name="location_scope" x-model="scope" class="form-select @error('location_scope') is-invalid @enderror">
                        <option value="inside_jordan">{{ __('tenders.location_inside_jordan') }}</option>
                        <option value="outside_jordan">{{ __('tenders.location_outside_jordan') }}</option>
                    </select>
                    @error('location_scope')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div x-show="scope === 'inside_jordan'">
                    <label class="form-label">{{ __('tenders.tender_governorate') }}</label>
                    <select name="governorate" class="js-select2 form-select @error('governorate') is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach(\App\Models\Tender::JORDAN_GOVERNORATES as $key => $names)
                            <option value="{{ $key }}" @selected(old('governorate', $purchaseRequest?->governorate) === $key)>{{ $names[app()->getLocale() === 'en' ? 'en' : 'ar'] }}</option>
                        @endforeach
                    </select>
                    @error('governorate')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div x-show="scope === 'outside_jordan'">
                    <label class="form-label">{{ __('tenders.tender_country') }}</label>
                    <select name="country_id" class="js-select2 form-select @error('country_id') is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" @selected(old('country_id', $purchaseRequest?->country_id) == $country->id)>{{ $country->localized_name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('external_purchases.request_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $purchaseRequest?->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-cyan-500 text-sm"></i>
                {{ __('external_purchases.request_items') }}
            </h3>
            <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                {{ __('accounting.invoice_add_item') }}
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('warehouse.voucher_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('external_purchases.item_ercd') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('accounting.invoice_item_unit_price') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-56">{{ __('external_purchases.item_features') }}</th>
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
                                <input type="text" :name="`items[${index}][ercd]`" x-model="item.ercd" dir="ltr" class="form-input">
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                       step="0.001" min="0" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="space-y-1">
                                    <template x-for="(feature, fIndex) in item.features" :key="fIndex">
                                        <div class="flex items-center gap-1">
                                            <input type="text" :name="`items[${index}][features][${fIndex}]`" x-model="item.features[fIndex]"
                                                   class="form-input !py-1 !text-xs" placeholder="{{ __('external_purchases.item_feature_placeholder') }}">
                                            <button type="button" @click="item.features.splice(fIndex, 1)"
                                                    class="p-1 text-slate-300 hover:text-rose-600 flex-shrink-0">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="item.features.push('')"
                                            class="text-xs font-semibold text-indigo-600 hover:underline">
                                        <i class="fa-solid fa-plus"></i> {{ __('external_purchases.add_feature') }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <button type="button" @click="removeItem(index)" title="{{ __('accounting.invoice_remove_item') }}"
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

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-note-sticky text-cyan-500 text-sm"></i>
                {{ __('external_purchases.additional_notes') }}
            </h3>
        </div>
        <div class="px-6 py-5 space-y-3">
            <template x-for="(note, nIndex) in additionalNotes" :key="nIndex">
                <div class="flex items-center gap-3">
                    <span class="w-56 flex-shrink-0 text-sm font-semibold text-slate-700" x-text="note.label"></span>
                    <input type="hidden" :name="`additional_notes[${nIndex}][label]`" :value="note.label">
                    <input type="text" :name="`additional_notes[${nIndex}][value]`" x-model="note.value" dir="ltr" class="form-input">
                    <button type="button" @click="additionalNotes.splice(nIndex, 1)"
                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all flex-shrink-0">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
            </template>
            <p x-show="additionalNotes.length === 0" class="text-sm text-slate-400">{{ __('external_purchases.no_additional_notes') }}</p>
        </div>
    </div>
</div>
