@php
    $analysis = $analysis ?? null;
    $seedItems = (
        $analysis?->items->map(fn ($i) => [
            'ciat_discount_id' => $i->ciat_discount_id, 'material_id' => $i->material_id, 'ciat_model' => $i->ciat_model, 'quantity' => (float) $i->quantity,
            'list_price' => (float) $i->list_price, 'profit_percent' => (float) $i->profit_percent, 'shipping' => (float) $i->shipping,
        ])->values()
        ?? collect([['ciat_discount_id' => '', 'material_id' => '', 'ciat_model' => '', 'quantity' => 1, 'list_price' => '', 'profit_percent' => '', 'shipping' => 0]])
    )->map(fn ($item, $i) => array_merge($item, ['_key' => $i + 1]))->values();
@endphp
<div x-data="{
        branches: {{ $branches->map(fn ($b) => ['id' => $b->id, 'taxRate' => (float) $b->ciat_tax_rate, 'jdRate' => (float) $b->ciat_jd_rate])->values()->toJson() }},
        ciatDiscounts: {{ $ciatDiscounts->map(fn ($d) => ['id' => $d->id, 'label' => $d->label, 'discount' => (float) $d->discount_percent])->values()->toJson() }},
        sourceType: '{{ old('source_type', $analysis?->source_type ?? 'ciat') }}',
        linkedType: '{{ old('linked_type', $analysis?->linked_type ?? '') }}',
        branchId: '{{ old('branch_id', $analysis?->branch_id) }}',
        taxRate: {{ (float) old('tax_rate', $analysis?->tax_rate ?? 16) }},
        jdRate: {{ (float) old('jd_rate', $analysis?->jd_rate ?? 0.82) }},
        withTax: {{ old('with_tax', $analysis?->with_tax) ? 'true' : 'false' }},
        onBranchChange() {
            const b = this.branches.find(x => String(x.id) === String(this.branchId));
            if (b) { this.taxRate = b.taxRate; this.jdRate = b.jdRate; }
        },
        onSourceTypeChange() { this.$nextTick(() => window.initSelect2()); },
        // _key is a stable per-row identity for Alpine's x-for :key — NOT array index. Keying by
        // index broke the select2 widget bound to whichever row shifted position after a delete
        // (select2's own injected DOM stays bound to a position, not the row's actual data, once
        // the array reorders) — see memory for the full diagnosis. Every row, however it enters
        // the array, must get a fresh _key.
        // _keySeq must start past the highest _key already used by the seeded rows below —
        // starting at 0 made the first addItem() reuse _key 1, a duplicate x-for :key that broke
        // Alpine's DOM diffing (reading 'after' on an undefined node).
        _keySeq: {{ $seedItems->count() }},
        nextKey() { return ++this._keySeq; },
        items: {{ $seedItems->toJson() }},
        addItem() { this.items.push({ ciat_discount_id: '', material_id: '', ciat_model: '', quantity: 1, list_price: '', profit_percent: '', shipping: 0, _key: this.nextKey() }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        discountFor(item) {
            if (this.sourceType === 'warehouse') return 0;
            const d = this.ciatDiscounts.find(x => String(x.id) === String(item.ciat_discount_id));
            return d ? d.discount : 0;
        },
        calc(item) {
            const discount = this.discountFor(item);
            const qty = parseFloat(item.quantity) || 0;
            const listPrice = parseFloat(item.list_price) || 0;
            const profitPercent = parseFloat(item.profit_percent) || 0;
            const shipping = parseFloat(item.shipping) || 0;
            const cost = listPrice * (1 - discount / 100);
            const profit = cost * profitPercent / 100;
            const price = cost + profit;
            const toJd = price * this.jdRate;
            return { discount, cost, profit, totalProfit: profit * qty, price, toJd, subtotal: toJd + shipping };
        },
        get subtotal() { return this.items.reduce((sum, item) => sum + this.calc(item).subtotal, 0); },
        get totalWithTax() { return this.subtotal * (1 + (this.taxRate / 100)); },
        fmt(n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 }); },
      }"
      x-init="$nextTick(() => window.initSelect2())">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-orange-500 text-sm"></i>
                {{ __('tenders.price_analyses_list') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('tenders.analysis_source_type') }} <span class="text-rose-500">*</span></label>
                <select name="source_type" x-model="sourceType" @change="onSourceTypeChange()" class="form-select @error('source_type') is-invalid @enderror">
                    <option value="ciat">{{ __('tenders.analysis_source_ciat') }}</option>
                    <option value="warehouse">{{ __('tenders.analysis_source_warehouse') }}</option>
                </select>
                @error('source_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('tenders.analysis_branch') }} <span class="text-rose-500">*</span></label>
                <select name="branch_id" x-model="branchId" @change="onBranchChange()" class="js-select2 form-select @error('branch_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $analysis?->branch_id) == $branch->id)>{{ $branch->localized_name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('tenders.analysis_linked_type') }}</label>
                <select name="linked_type" x-model="linkedType" class="form-select @error('linked_type') is-invalid @enderror">
                    <option value="">{{ __('tenders.analysis_linked_none') }}</option>
                    <option value="price_quote">{{ __('tenders.analysis_linked_price_quote') }}</option>
                    <option value="project">{{ __('tenders.analysis_linked_project') }}</option>
                </select>
                @error('linked_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <template x-if="linkedType === 'price_quote'">
                    <div>
                        <label class="form-label">{{ __('tenders.analysis_linked_price_quote') }}</label>
                        <select name="linked_id" class="js-select2 form-select @error('linked_id') is-invalid @enderror">
                            <option value="">{{ __('app.select') }}</option>
                            @foreach($priceQuotes as $priceQuote)
                                <option value="{{ $priceQuote->id }}" @selected(old('linked_id', $analysis?->linked_type === 'price_quote' ? $analysis?->linked_id : null) == $priceQuote->id)>{{ $priceQuote->number }} — {{ $priceQuote->customer?->localized_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>
                <template x-if="linkedType === 'project'">
                    <div>
                        <label class="form-label">{{ __('tenders.analysis_linked_project') }}</label>
                        <select name="linked_id" class="js-select2 form-select @error('linked_id') is-invalid @enderror">
                            <option value="">{{ __('app.select') }}</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('linked_id', $analysis?->linked_type === 'project' ? $analysis?->linked_id : null) == $project->id)>{{ $project->number }} — {{ $project->localized_title }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>
                @error('linked_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="with_tax" value="0">
                    <input type="checkbox" name="with_tax" value="1" x-model="withTax" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('tenders.analysis_with_tax') }}</span>
                </label>
            </div>

            <div class="sm:col-span-2 flex flex-wrap items-center gap-4">
                <p class="text-xs text-slate-400" x-text="'{{ __('tenders.analysis_with_tax_hint_before') }}' + taxRate + '{{ __('tenders.analysis_with_tax_hint_after') }}'"></p>
                <p class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg" x-show="branchId" x-cloak>
                    <i class="fa-solid fa-coins text-amber-500"></i>
                    {{ __('tenders.analysis_jd_rate') }}: <span dir="ltr" x-text="jdRate"></span>
                </p>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('tenders.analysis_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $analysis?->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-orange-500 text-sm"></i>
                {{ __('tenders.analysis_item_product') }}
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
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('tenders.analysis_item_product') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('tenders.ciat_model') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-20">{{ __('tenders.analysis_item_quantity') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28">{{ __('tenders.analysis_item_list_price') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_cost') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-20">{{ __('tenders.analysis_item_profit_percent') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_profit') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_total_profit') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_price') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_to_jd') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_shipping') }}</th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24">{{ __('tenders.analysis_item_subtotal') }}</th>
                        <th class="px-3 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="item._key">
                        <tr>
                            <td class="px-3 py-2.5">
                                <template x-if="sourceType === 'ciat'">
                                    <select :name="`items[${index}][ciat_discount_id]`" x-model="item.ciat_discount_id" class="js-select2 form-select !text-xs" required>
                                        <option value="">{{ __('app.select') }}</option>
                                        @foreach($ciatDiscounts as $ciatDiscount)
                                            <option value="{{ $ciatDiscount->id }}">{{ $ciatDiscount->label }}</option>
                                        @endforeach
                                    </select>
                                </template>
                                <template x-if="sourceType === 'warehouse'">
                                    <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="js-select2 form-select !text-xs" required>
                                        <option value="">{{ __('app.select') }}</option>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}">{{ $material->localized_name }}</option>
                                        @endforeach
                                    </select>
                                </template>
                            </td>
                            <td class="px-3 py-2.5" x-show="sourceType === 'ciat'">
                                <input type="text" :name="`items[${index}][ciat_model]`" x-model="item.ciat_model"
                                       dir="ltr" class="form-input !text-xs" :required="sourceType === 'ciat'">
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input !text-xs" required>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][list_price]`" x-model="item.list_price"
                                       step="0.001" min="0" dir="ltr" class="form-input !text-xs" required>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).cost)"></td>
                            <td class="px-3 py-2.5">
                                <div class="relative">
                                    <input type="number" :name="`items[${index}][profit_percent]`" x-model="item.profit_percent"
                                           step="0.01" dir="ltr" class="form-input !text-xs !pe-5" required>
                                    <span class="absolute top-1/2 -translate-y-1/2 end-2 text-slate-400 text-xs">%</span>
                                </div>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).profit)"></td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).totalProfit)"></td>
                            <td class="px-3 py-2.5 text-xs font-bold text-slate-700" dir="ltr" x-text="fmt(calc(item).price)"></td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).toJd)"></td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][shipping]`" x-model="item.shipping"
                                       step="0.001" min="0" dir="ltr" class="form-input !text-xs">
                            </td>
                            <td class="px-3 py-2.5 text-xs font-bold text-orange-700" dir="ltr" x-text="fmt(calc(item).subtotal)"></td>
                            <td class="px-3 py-2.5 text-center">
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

    <div class="flex justify-end mb-5">
        <div class="card px-6 py-5 w-full sm:w-96">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500 font-medium">{{ __('tenders.analysis_item_subtotal') }}</dt>
                    <dd class="font-bold text-slate-800" dir="ltr" x-text="fmt(subtotal)"></dd>
                </div>
                <div class="flex justify-between" x-show="withTax">
                    <dt class="text-slate-700 font-bold">{{ __('tenders.analysis_total_with_tax') }}</dt>
                    <dd class="font-black text-lg text-orange-700" dir="ltr" x-text="fmt(totalWithTax)"></dd>
                </div>
            </dl>
        </div>
    </div>
</div>
