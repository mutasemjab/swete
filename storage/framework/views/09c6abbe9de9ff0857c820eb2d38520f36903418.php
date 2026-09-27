<?php $analysis = $analysis ?? null; ?>
<div x-data="{
        branches: <?php echo e($branches->map(fn ($b) => ['id' => $b->id, 'taxRate' => (float) $b->ciat_tax_rate, 'jdRate' => (float) $b->ciat_jd_rate])->values()->toJson()); ?>,
        ciatDiscounts: <?php echo e($ciatDiscounts->map(fn ($d) => ['id' => $d->id, 'label' => $d->label, 'discount' => (float) $d->discount_percent])->values()->toJson()); ?>,
        branchId: '<?php echo e(old('branch_id', $analysis?->branch_id)); ?>',
        taxRate: <?php echo e((float) old('tax_rate', $analysis?->tax_rate ?? 16)); ?>,
        jdRate: <?php echo e((float) old('jd_rate', $analysis?->jd_rate ?? 0.82)); ?>,
        withTax: <?php echo e(old('with_tax', $analysis?->with_tax) ? 'true' : 'false'); ?>,
        onBranchChange() {
            const b = this.branches.find(x => String(x.id) === String(this.branchId));
            if (b) { this.taxRate = b.taxRate; this.jdRate = b.jdRate; }
        },
        items: <?php echo e((
            $analysis?->items->map(fn ($i) => [
                'ciat_discount_id' => $i->ciat_discount_id, 'quantity' => (float) $i->quantity,
                'list_price' => (float) $i->list_price, 'profit' => (float) $i->profit, 'shipping' => (float) $i->shipping,
            ])->values()
            ?? collect([['ciat_discount_id' => '', 'quantity' => 1, 'list_price' => '', 'profit' => '', 'shipping' => 0]])
        )->toJson()); ?>,
        addItem() { this.items.push({ ciat_discount_id: '', quantity: 1, list_price: '', profit: '', shipping: 0 }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        discountFor(id) { const d = this.ciatDiscounts.find(x => String(x.id) === String(id)); return d ? d.discount : 0; },
        calc(item) {
            const discount = this.discountFor(item.ciat_discount_id);
            const qty = parseFloat(item.quantity) || 0;
            const listPrice = parseFloat(item.list_price) || 0;
            const profit = parseFloat(item.profit) || 0;
            const shipping = parseFloat(item.shipping) || 0;
            const cost = listPrice * (1 - discount / 100);
            const price = cost + profit;
            const toJd = price * this.jdRate;
            return { discount, cost, totalProfit: profit * qty, price, toJd, subtotal: toJd + shipping };
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
                <?php echo e(__('tenders.price_analyses_list')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('tenders.analysis_branch')); ?> <span class="text-rose-500">*</span></label>
                <select name="branch_id" x-model="branchId" @change="onBranchChange()" class="js-select2 form-select <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>" <?php if(old('branch_id', $analysis?->branch_id) == $branch->id): echo 'selected'; endif; ?>><?php echo e($branch->localized_name); ?></option>
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

            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="with_tax" value="0">
                    <input type="checkbox" name="with_tax" value="1" x-model="withTax" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('tenders.analysis_with_tax')); ?></span>
                </label>
            </div>

            <div class="sm:col-span-2">
                <p class="text-xs text-slate-400" x-text="'<?php echo e(__('tenders.analysis_with_tax_hint_before')); ?>' + taxRate + '<?php echo e(__('tenders.analysis_with_tax_hint_after')); ?>'"></p>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label"><?php echo e(__('tenders.analysis_notes')); ?></label>
                <textarea name="notes" rows="2" class="form-input <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $analysis?->notes)); ?></textarea>
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
                <i class="fa-solid fa-list text-orange-500 text-sm"></i>
                <?php echo e(__('tenders.analysis_item_product')); ?>

            </h3>
            <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('accounting.invoice_add_item')); ?>

            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_product')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-20"><?php echo e(__('tenders.analysis_item_quantity')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-28"><?php echo e(__('tenders.analysis_item_list_price')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-16"><?php echo e(__('tenders.analysis_item_discount')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_cost')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_profit')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_total_profit')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_price')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_to_jd')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_shipping')); ?></th>
                        <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-24"><?php echo e(__('tenders.analysis_item_subtotal')); ?></th>
                        <th class="px-3 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-3 py-2.5">
                                <select :name="`items[${index}][ciat_discount_id]`" x-model="item.ciat_discount_id" class="js-select2 form-select !text-xs" required>
                                    <option value=""><?php echo e(__('app.select')); ?></option>
                                    <?php $__currentLoopData = $ciatDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ciatDiscount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($ciatDiscount->id); ?>"><?php echo e($ciatDiscount->label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input !text-xs" required>
                            </td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][list_price]`" x-model="item.list_price"
                                       step="0.001" min="0" dir="ltr" class="form-input !text-xs" required>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="calc(item).discount + '%'"></td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).cost)"></td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][profit]`" x-model="item.profit"
                                       step="0.001" dir="ltr" class="form-input !text-xs" required>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).totalProfit)"></td>
                            <td class="px-3 py-2.5 text-xs font-bold text-slate-700" dir="ltr" x-text="fmt(calc(item).price)"></td>
                            <td class="px-3 py-2.5 text-xs text-slate-600" dir="ltr" x-text="fmt(calc(item).toJd)"></td>
                            <td class="px-3 py-2.5">
                                <input type="number" :name="`items[${index}][shipping]`" x-model="item.shipping"
                                       step="0.001" min="0" dir="ltr" class="form-input !text-xs">
                            </td>
                            <td class="px-3 py-2.5 text-xs font-bold text-orange-700" dir="ltr" x-text="fmt(calc(item).subtotal)"></td>
                            <td class="px-3 py-2.5 text-center">
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

    <div class="flex justify-end mb-5">
        <div class="card px-6 py-5 w-full sm:w-96">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500 font-medium"><?php echo e(__('tenders.analysis_item_subtotal')); ?></dt>
                    <dd class="font-bold text-slate-800" dir="ltr" x-text="fmt(subtotal)"></dd>
                </div>
                <div class="flex justify-between" x-show="withTax">
                    <dt class="text-slate-700 font-bold"><?php echo e(__('tenders.analysis_total_with_tax')); ?></dt>
                    <dd class="font-black text-lg text-orange-700" dir="ltr" x-text="fmt(totalWithTax)"></dd>
                </div>
            </dl>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-analyses/_form.blade.php ENDPATH**/ ?>