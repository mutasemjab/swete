@php $priceQuote = $priceQuote ?? null; @endphp
<div x-data="{
        items: {{ (
            $priceQuote?->items->map(fn ($i) => [
                'material_id' => $i->material_id,
                'quantity'    => (float) $i->quantity,
                'unit_price'  => (float) $i->unit_price,
                'notes'       => $i->notes ?: [''],
            ])->values()
            ?? collect([['material_id' => '', 'quantity' => '', 'unit_price' => '', 'notes' => ['']]])
        )->toJson() }},
        addItem() { this.items.push({ material_id: '', quantity: '', unit_price: '', notes: [''] }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        discountType: '{{ old('discount_type', $priceQuote?->discount_type ?? 'amount') }}',
        discountValue: {{ (float) old('discount_value', $priceQuote?->discount_value ?? 0) }},
        get subtotal() { return this.items.reduce((sum, i) => sum + (parseFloat(i.quantity) || 0) * (parseFloat(i.unit_price) || 0), 0); },
        get discountAmount() {
            const value = parseFloat(this.discountValue) || 0;
            const amount = this.discountType === 'percent' ? this.subtotal * value / 100 : value;
            return Math.min(Math.max(amount, 0), this.subtotal);
        },
        get total() { return this.subtotal - this.discountAmount; },
        fmt(n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 }); },
      }"
      x-init="$nextTick(() => window.initSelect2())">

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice text-orange-500 text-sm"></i>
            {{ __('tenders.price_quote') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        @if($tender)
        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.quote_tender') }}</label>
            <p class="font-bold text-slate-800">{{ $tender->number }} — {{ $tender->localized_title }}</p>
        </div>
        @endif

        <div>
            <label class="form-label">{{ __('accounting.customer') }} <span class="text-rose-500">*</span></label>
            <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id', $priceQuote?->customer_id ?? $tender?->party_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                @endforeach
            </select>
            @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.quote_date') }} <span class="text-rose-500">*</span></label>
            <input type="date" name="date" value="{{ old('date', $priceQuote?->date?->toDateString() ?? now()->toDateString()) }}"
                   class="form-input @error('date') is-invalid @enderror">
            @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('external_purchases.request_branch') }} <span class="text-rose-500">*</span></label>
            <select name="branch_id" class="js-select2 form-select @error('branch_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id', $priceQuote?->branch_id) == $branch->id)>{{ $branch->localized_name }}</option>
                @endforeach
            </select>
            @error('branch_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_currency') }} <span class="text-rose-500">*</span></label>
            <select name="currency_id" class="js-select2 form-select @error('currency_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}" @selected(old('currency_id', $priceQuote?->currency_id ?? $currencies->firstWhere('is_default', true)?->id) == $currency->id)>{{ $currency->localized_name }} ({{ $currency->code }})</option>
                @endforeach
            </select>
            @error('currency_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('accounting.invoice_notes') }}</label>
            <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $priceQuote?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-signature text-orange-500 text-sm"></i>
            {{ __('tenders.quote_commercial_terms') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('tenders.quote_validity_weeks') }}</label>
            <input type="number" name="validity_weeks" value="{{ old('validity_weeks', $priceQuote?->validity_weeks) }}"
                   min="1" dir="ltr" class="form-input @error('validity_weeks') is-invalid @enderror">
            @error('validity_weeks')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.quote_supply_scope') }}</label>
            <select name="supply_scope_id" class="js-select2 form-select @error('supply_scope_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($supplyScopes as $item)
                    <option value="{{ $item->id }}" @selected(old('supply_scope_id', $priceQuote?->supply_scope_id) == $item->id)>{{ $item->localized_name }}</option>
                @endforeach
            </select>
            @error('supply_scope_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.quote_delivery_term') }}</label>
            <select name="delivery_term_id" class="js-select2 form-select @error('delivery_term_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($deliveryTerms as $item)
                    <option value="{{ $item->id }}" @selected(old('delivery_term_id', $priceQuote?->delivery_term_id) == $item->id)>{{ $item->localized_name }}</option>
                @endforeach
            </select>
            @error('delivery_term_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2 flex flex-wrap items-center gap-6 py-1">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="winching_included" value="0">
                <input type="checkbox" name="winching_included" value="1" class="sr-only peer" @checked(old('winching_included', $priceQuote?->winching_included))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('tenders.quote_winching_included') }}</span>
            </label>

            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="sales_tax_included" value="0">
                <input type="checkbox" name="sales_tax_included" value="1" class="sr-only peer" @checked(old('sales_tax_included', $priceQuote?->sales_tax_included))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('tenders.quote_sales_tax_included') }}</span>
            </label>

            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="customs_fees_included" value="0">
                <input type="checkbox" name="customs_fees_included" value="1" class="sr-only peer" @checked(old('customs_fees_included', $priceQuote?->customs_fees_included))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('tenders.quote_customs_fees_included') }}</span>
            </label>

            
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.quote_included_work_scopes') }}</label>
            <p class="text-xs text-slate-400 mb-2">{{ __('tenders.quote_included_work_scopes_hint') }}</p>
            @php $includedScopes = old('included_work_scopes', $priceQuote?->included_work_scopes ?? array_keys(\App\Models\PriceQuote::WORK_SCOPE_ITEMS)); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach(\App\Models\PriceQuote::WORK_SCOPE_ITEMS as $key => $labels)
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="included_work_scopes[]" value="{{ $key }}"
                           @checked(in_array($key, $includedScopes))
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    {{ __('tenders.quote_work_scope_' . $key) }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.quote_additional_terms') }}</label>
            <p class="text-xs text-slate-400 mb-2">{{ __('tenders.quote_additional_terms_hint') }}</p>
            <textarea name="additional_terms" rows="3"
                      class="form-input @error('additional_terms') is-invalid @enderror">{{ old('additional_terms', $priceQuote?->additional_terms) }}</textarea>
            @error('additional_terms')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-list text-orange-500 text-sm"></i>
            {{ __('tenders.quote_items') }}
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
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('accounting.invoice_item_unit_price') }}</th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-72">{{ __('tenders.quote_item_notes') }}</th>
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
                                   step="0.001" min="0" dir="ltr" class="form-input" required>
                        </td>
                        <td class="px-5 py-2.5">
                            <div class="space-y-1">
                                <template x-for="(note, nIndex) in item.notes" :key="nIndex">
                                    <div class="flex items-center gap-1">
                                        <input type="text" :name="`items[${index}][notes][${nIndex}]`" x-model="item.notes[nIndex]"
                                               class="form-input !py-1 !text-xs" placeholder="{{ __('tenders.quote_item_note_placeholder') }}">
                                        <button type="button" @click="item.notes.splice(nIndex, 1)"
                                                class="p-1 text-slate-300 hover:text-rose-600 flex-shrink-0">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="item.notes.push('')"
                                        class="text-xs font-semibold text-indigo-600 hover:underline">
                                    <i class="fa-solid fa-plus"></i> {{ __('tenders.quote_add_item_note') }}
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
            <i class="fa-solid fa-calculator text-orange-500 text-sm"></i>
            {{ __('tenders.quote_summary') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
        <div>
            <label class="form-label">{{ __('tenders.quote_discount') }}</label>
            <div class="grid grid-cols-3 gap-2">
                <select name="discount_type" x-model="discountType" class="form-select @error('discount_type') is-invalid @enderror">
                    <option value="amount">{{ __('tenders.quote_discount_amount') }}</option>
                    <option value="percent">{{ __('tenders.quote_discount_percent') }}</option>
                </select>
                <input type="number" name="discount_value" x-model="discountValue" step="0.001" min="0" dir="ltr"
                       :max="discountType === 'percent' ? 100 : null"
                       class="form-input col-span-2 @error('discount_value') is-invalid @enderror">
            </div>
            @error('discount_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @error('discount_value')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
        <div class="hidden sm:block h-[26px]" aria-hidden="true"></div>
        <dl class="space-y-2 text-sm bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium">{{ __('tenders.quote_subtotal') }}</dt>
                <dd class="font-bold text-slate-800" dir="ltr" x-text="fmt(subtotal)"></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium">{{ __('tenders.quote_discount') }}</dt>
                <dd class="font-bold text-rose-600" dir="ltr" x-text="'- ' + fmt(discountAmount)"></dd>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <dt class="text-slate-700 font-bold">{{ __('tenders.quote_total') }}</dt>
                <dd class="font-black text-lg text-orange-700" dir="ltr" x-text="fmt(total)"></dd>
            </div>
        </dl>
        </div>
    </div>
</div>

</div>
