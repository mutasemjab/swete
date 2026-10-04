<?php $priceQuote = $priceQuote ?? null; $report = $report ?? null; ?>
<div x-data="{
        // _key is a stable per-row identity for Alpine's x-for :key — NOT array index. Keying by
        // index broke the select2 widget bound to whichever row shifted position after a delete
        // (select2's own injected DOM stays bound to a position, not the row's actual data, once
        // the array reorders). Every row, however it enters the array, must get a fresh _key.
        _keySeq: 0,
        nextKey() { return ++this._keySeq; },
        items: <?php echo e((
            (
                $priceQuote?->items->map(fn ($i) => [
                    'material_id' => $i->material_id,
                    'quantity'    => (float) $i->quantity,
                    'unit_price'  => (float) $i->unit_price,
                    'notes'       => $i->notes ?: [''],
                ])->values()
                ?? ($report?->materials->isNotEmpty() ? $report->materials->map(fn ($m) => [
                    'material_id' => $m->material_id,
                    'quantity'    => (float) $m->quantity,
                    'unit_price'  => 0,
                    'notes'       => [''],
                ])->values() : null)
                ?? collect([['material_id' => '', 'quantity' => '', 'unit_price' => '', 'notes' => ['']]])
            )->map(fn ($item, $i) => array_merge($item, ['_key' => $i + 1]))->values()
        )->toJson()); ?>,
        addItem() { this.items.push({ material_id: '', quantity: '', unit_price: '', notes: [''], _key: this.nextKey() }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        discountType: '<?php echo e(old('discount_type', $priceQuote?->discount_type ?? 'amount')); ?>',
        discountValue: <?php echo e((float) old('discount_value', $priceQuote?->discount_value ?? 0)); ?>,
        get subtotal() { return this.items.reduce((sum, i) => sum + (parseFloat(i.quantity) || 0) * (parseFloat(i.unit_price) || 0), 0); },
        get discountAmount() {
            const value = parseFloat(this.discountValue) || 0;
            const amount = this.discountType === 'percent' ? this.subtotal * value / 100 : value;
            return Math.min(Math.max(amount, 0), this.subtotal);
        },
        get total() { return this.subtotal - this.discountAmount; },
        fmt(n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits: 3, maximumFractionDigits: 3 }); },
        customerId: '<?php echo e(old('customer_id', $priceQuote?->customer_id ?? $tender?->party_id ?? $report?->customer_id)); ?>',
        customerHistory: <?php echo e($customerQuoteHistory->toJson()); ?>,
        get customerHistoryList() { return this.customerHistory[this.customerId] || []; },
        materialHistory: <?php echo e($materialPriceHistory->toJson()); ?>,
        materialStock: <?php echo e($materialStock->toJson()); ?>,
        priceAnalyses: <?php echo e($priceAnalyses->toJson()); ?>,
        selectedAnalysisId: '',
        showAnalysisPicker: false,
        get selectedAnalysisItems() {
            const a = this.priceAnalyses.find(x => String(x.id) === String(this.selectedAnalysisId));
            return a ? a.items : [];
        },
        addFromAnalysis(analysisItem) {
            const newItem = {
                material_id: analysisItem.material_id, quantity: analysisItem.quantity,
                unit_price: analysisItem.unit_price, notes: analysisItem.ciat_model ? [analysisItem.ciat_model] : [''],
                _key: this.nextKey(),
            };
            // A brand-new quote starts with one untouched empty placeholder row — fill it instead
            // of appending after it, so "choose from analysis" never leaves a stray empty/invalid
            // row the user has to remember to delete themselves.
            if (this.items.length === 1 && !this.items[0].material_id) {
                this.items[0] = newItem;
            } else {
                this.items.push(newItem);
            }
            this.$nextTick(() => window.initSelect2());
        },
      }"
      x-init="$nextTick(() => window.initSelect2())">

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice text-orange-500 text-sm"></i>
            <?php echo e(__('tenders.price_quote')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <?php if($tender): ?>
        <div class="sm:col-span-2">
            <label class="form-label"><?php echo e(__('tenders.quote_tender')); ?></label>
            <p class="font-bold text-slate-800"><?php echo e($tender->number); ?> — <?php echo e($tender->localized_title); ?></p>
        </div>
        <?php endif; ?>

        <div>
            <label class="form-label"><?php echo e(__('accounting.customer')); ?> <span class="text-rose-500">*</span></label>
            <select name="customer_id" x-model="customerId" class="js-select2 form-select <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($customer->id); ?>" <?php if(old('customer_id', $priceQuote?->customer_id ?? $tender?->party_id ?? $report?->customer_id) == $customer->id): echo 'selected'; endif; ?>><?php echo e($customer->localized_name); ?> (<?php echo e($customer->code); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2" x-show="customerId" x-cloak>
            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                <p class="text-xs font-bold text-slate-500 mb-2">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <?php echo e(__('tenders.quote_customer_history')); ?>

                </p>
                <template x-if="customerHistoryList.length === 0">
                    <p class="text-xs text-slate-400"><?php echo e(__('tenders.quote_customer_history_empty')); ?></p>
                </template>
                <ul class="space-y-1">
                    <template x-for="q in customerHistoryList" :key="q.number">
                        <li class="flex items-center justify-between text-xs">
                            <a :href="q.url" target="_blank" class="text-indigo-600 hover:underline font-mono font-bold" x-text="q.number"></a>
                            <span class="text-slate-500" dir="ltr" x-text="q.date"></span>
                            <span class="font-bold text-slate-700" dir="ltr" x-text="fmt(q.total) + ' ' + (q.currency || '')"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('tenders.quote_date')); ?> <span class="text-rose-500">*</span></label>
            <input type="date" name="date" value="<?php echo e(old('date', $priceQuote?->date?->toDateString() ?? now()->toDateString())); ?>"
                   class="form-input <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('external_purchases.request_branch')); ?> <span class="text-rose-500">*</span></label>
            <select name="branch_id" class="js-select2 form-select <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($branch->id); ?>" <?php if(old('branch_id', $priceQuote?->branch_id) == $branch->id): echo 'selected'; endif; ?>><?php echo e($branch->localized_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('tenders.tender_currency')); ?> <span class="text-rose-500">*</span></label>
            <select name="currency_id" class="js-select2 form-select <?php $__errorArgs = ['currency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($currency->id); ?>" <?php if(old('currency_id', $priceQuote?->currency_id ?? $currencies->firstWhere('is_default', true)?->id) == $currency->id): echo 'selected'; endif; ?>><?php echo e($currency->localized_name); ?> (<?php echo e($currency->code); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['currency_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2">
            <label class="form-label"><?php echo e(__('accounting.invoice_notes')); ?></label>
            <textarea name="notes" rows="2" class="form-input <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $priceQuote?->notes)); ?></textarea>
            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-signature text-orange-500 text-sm"></i>
            <?php echo e(__('tenders.quote_commercial_terms')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label"><?php echo e(__('tenders.quote_validity_weeks')); ?></label>
            <input type="number" name="validity_weeks" value="<?php echo e(old('validity_weeks', $priceQuote?->validity_weeks)); ?>"
                   min="1" dir="ltr" class="form-input <?php $__errorArgs = ['validity_weeks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['validity_weeks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('tenders.quote_supply_scope')); ?></label>
            <select name="supply_scope_id" class="js-select2 form-select <?php $__errorArgs = ['supply_scope_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $supplyScopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>" <?php if(old('supply_scope_id', $priceQuote?->supply_scope_id) == $item->id): echo 'selected'; endif; ?>><?php echo e($item->localized_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['supply_scope_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('tenders.quote_delivery_term')); ?></label>
            <select name="delivery_term_id" class="js-select2 form-select <?php $__errorArgs = ['delivery_term_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $deliveryTerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>" <?php if(old('delivery_term_id', $priceQuote?->delivery_term_id) == $item->id): echo 'selected'; endif; ?>><?php echo e($item->localized_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['delivery_term_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="sm:col-span-2 flex flex-wrap items-center gap-6 py-1">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="winching_included" value="0">
                <input type="checkbox" name="winching_included" value="1" class="sr-only peer" <?php if(old('winching_included', $priceQuote?->winching_included)): echo 'checked'; endif; ?>>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('tenders.quote_winching_included')); ?></span>
            </label>

            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="sales_tax_included" value="0">
                <input type="checkbox" name="sales_tax_included" value="1" class="sr-only peer" <?php if(old('sales_tax_included', $priceQuote?->sales_tax_included)): echo 'checked'; endif; ?>>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('tenders.quote_sales_tax_included')); ?></span>
            </label>

            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="customs_fees_included" value="0">
                <input type="checkbox" name="customs_fees_included" value="1" class="sr-only peer" <?php if(old('customs_fees_included', $priceQuote?->customs_fees_included)): echo 'checked'; endif; ?>>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('tenders.quote_customs_fees_included')); ?></span>
            </label>

            
        </div>

        <div class="sm:col-span-2">
            <label class="form-label"><?php echo e(__('tenders.quote_included_work_scopes')); ?></label>
            <p class="text-xs text-slate-400 mb-2"><?php echo e(__('tenders.quote_included_work_scopes_hint')); ?></p>
            <?php $includedScopes = old('included_work_scopes', $priceQuote?->included_work_scopes ?? array_keys(\App\Models\PriceQuote::WORK_SCOPE_ITEMS)); ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <?php $__currentLoopData = \App\Models\PriceQuote::WORK_SCOPE_ITEMS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $labels): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="included_work_scopes[]" value="<?php echo e($key); ?>"
                           <?php if(in_array($key, $includedScopes)): echo 'checked'; endif; ?>
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <?php echo e(__('tenders.quote_work_scope_' . $key)); ?>

                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="sm:col-span-2">
            <label class="form-label"><?php echo e(__('tenders.quote_additional_terms')); ?></label>
            <p class="text-xs text-slate-400 mb-2"><?php echo e(__('tenders.quote_additional_terms_hint')); ?></p>
            <textarea name="additional_terms" rows="3"
                      class="form-input <?php $__errorArgs = ['additional_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('additional_terms', $priceQuote?->additional_terms)); ?></textarea>
            <?php $__errorArgs = ['additional_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-list text-orange-500 text-sm"></i>
            <?php echo e(__('tenders.quote_items')); ?>

        </h3>
        <div class="flex items-center gap-2">
            <button type="button" @click="showAnalysisPicker = true" class="btn-secondary btn-sm">
                <i class="fa-solid fa-chart-line"></i>
                <?php echo e(__('tenders.quote_choose_from_analysis')); ?>

            </button>
            <?php echo $__env->make('components.material-quick-add-modal', [
                'targetSelector' => 'select[name$="[material_id]"]',
                'categories'     => $materialCategories,
                'units'          => $units,
                'label'          => __('warehouse.add_material_quick'),
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('accounting.invoice_add_item')); ?>

            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.material')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-72"><?php echo e(__('tenders.quote_item_notes')); ?></th>
                    <th class="px-5 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="(item, index) in items" :key="item._key">
                    <tr>
                        <td class="px-5 py-2.5">
                            <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="js-select2 form-select" required>
                                <option value=""><?php echo e(__('app.select')); ?></option>
                                <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($material->id); ?>"><?php echo e($material->localized_name); ?> (<?php echo e($material->code); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <template x-if="item.material_id">
                                <div class="mt-1.5 text-[11px] text-slate-500 space-y-1">
                                    <p>
                                        <i class="fa-solid fa-warehouse text-slate-400"></i>
                                        <?php echo e(__('tenders.quote_material_current_stock')); ?>:
                                        <span class="font-bold text-slate-700" x-text="fmt(materialStock[item.material_id] || 0)"></span>
                                    </p>
                                    <template x-if="(materialHistory[item.material_id] || []).length">
                                        <details>
                                            <summary class="text-indigo-600 hover:underline cursor-pointer">
                                                <?php echo e(__('tenders.quote_material_price_history')); ?>

                                                (<span x-text="(materialHistory[item.material_id] || []).length"></span>)
                                            </summary>
                                            <ul class="mt-1 space-y-0.5 ps-3">
                                                <template x-for="h in (materialHistory[item.material_id] || [])" :key="h.number">
                                                    <li dir="ltr" x-text="h.date + ' — ' + h.number + ' — ' + fmt(h.unit_price)"></li>
                                                </template>
                                            </ul>
                                        </details>
                                    </template>
                                    <p x-show="!(materialHistory[item.material_id] || []).length" class="text-slate-400"><?php echo e(__('tenders.quote_material_no_history')); ?></p>
                                </div>
                            </template>
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
                                               class="form-input !py-1 !text-xs" placeholder="<?php echo e(__('tenders.quote_item_note_placeholder')); ?>">
                                        <button type="button" @click="item.notes.splice(nIndex, 1)"
                                                class="p-1 text-slate-300 hover:text-rose-600 flex-shrink-0">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="item.notes.push('')"
                                        class="text-xs font-semibold text-indigo-600 hover:underline">
                                    <i class="fa-solid fa-plus"></i> <?php echo e(__('tenders.quote_add_item_note')); ?>

                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-2.5 text-center">
                            <button type="button" @click="removeItem(index)" title="<?php echo e(__('accounting.invoice_remove_item')); ?>"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-calculator text-orange-500 text-sm"></i>
            <?php echo e(__('tenders.quote_summary')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
        <div>
            <label class="form-label"><?php echo e(__('tenders.quote_discount')); ?></label>
            <div class="grid grid-cols-3 gap-2">
                <select name="discount_type" x-model="discountType" class="form-select <?php $__errorArgs = ['discount_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="amount"><?php echo e(__('tenders.quote_discount_amount')); ?></option>
                    <option value="percent"><?php echo e(__('tenders.quote_discount_percent')); ?></option>
                </select>
                <input type="number" name="discount_value" x-model="discountValue" step="0.001" min="0" dir="ltr"
                       :max="discountType === 'percent' ? 100 : null"
                       class="form-input col-span-2 <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            </div>
            <?php $__errorArgs = ['discount_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
        <div class="hidden sm:block h-[26px]" aria-hidden="true"></div>
        <dl class="space-y-2 text-sm bg-slate-50 border border-slate-100 rounded-xl px-5 py-4">
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium"><?php echo e(__('tenders.quote_subtotal')); ?></dt>
                <dd class="font-bold text-slate-800" dir="ltr" x-text="fmt(subtotal)"></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium"><?php echo e(__('tenders.quote_discount')); ?></dt>
                <dd class="font-bold text-rose-600" dir="ltr" x-text="'- ' + fmt(discountAmount)"></dd>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <dt class="text-slate-700 font-bold"><?php echo e(__('tenders.quote_total')); ?></dt>
                <dd class="font-black text-lg text-orange-700" dir="ltr" x-text="fmt(total)"></dd>
            </div>
        </dl>
        </div>
    </div>
</div>


<div x-show="showAnalysisPicker" x-cloak
     class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div @click.outside="showAnalysisPicker = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800"><?php echo e(__('tenders.quote_choose_from_analysis')); ?></h3>
            <button type="button" @click="showAnalysisPicker = false" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="px-6 py-4 border-b border-slate-100">
            <label class="form-label"><?php echo e(__('tenders.price_analyses_list')); ?></label>
            <select x-model="selectedAnalysisId" class="form-select">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <template x-for="a in priceAnalyses" :key="a.id">
                    <option :value="a.id" x-text="a.number"></option>
                </template>
            </select>
        </div>
        <div class="px-6 py-4 overflow-y-auto flex-1">
            <template x-if="selectedAnalysisId && selectedAnalysisItems.length === 0">
                <p class="text-sm text-slate-400 text-center py-6"><?php echo e(__('tenders.quote_analysis_no_items')); ?></p>
            </template>
            <template x-if="!selectedAnalysisId">
                <p class="text-sm text-slate-400 text-center py-6"><?php echo e(__('tenders.quote_select_analysis_first')); ?></p>
            </template>
            <div class="space-y-2">
                <template x-for="(analysisItem, aIndex) in selectedAnalysisItems" :key="aIndex">
                    <div class="flex items-center justify-between gap-3 border border-slate-200 rounded-xl px-4 py-2.5">
                        <div>
                            <p class="font-bold text-slate-800 text-sm" x-text="analysisItem.material_name"></p>
                            <p class="text-xs text-slate-400" dir="ltr" x-text="analysisItem.ciat_model + ' — Qty: ' + analysisItem.quantity + ' — ' + fmt(analysisItem.unit_price)"></p>
                        </div>
                        <button type="button" @click="addFromAnalysis(analysisItem)" class="btn-secondary btn-sm flex-shrink-0">
                            <i class="fa-solid fa-plus"></i>
                            <?php echo e(__('app.add')); ?>

                        </button>
                    </div>
                </template>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end">
            <button type="button" @click="showAnalysisPicker = false" class="btn-secondary"><?php echo e(__('app.close')); ?></button>
        </div>
    </div>
</div>

</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-quotes/_form.blade.php ENDPATH**/ ?>