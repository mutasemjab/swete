

<?php $__env->startSection('title', $priceQuote->number); ?>
<?php $__env->startSection('breadcrumb', $priceQuote->number); ?>

<?php $__env->startSection('content'); ?>

<?php if (! ($priceQuote->is_current)): ?>
    <div class="card !bg-amber-50 !border-amber-200 px-5 py-4 mb-5 flex items-center justify-between gap-3">
        <p class="font-bold text-amber-800">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php echo e(__('tenders.quote_old_version_banner')); ?>

        </p>
        <?php if($priceQuote->root): ?>
            <a href="<?php echo e(route('price-quotes.show', $priceQuote->root)); ?>" class="btn-secondary btn-sm">
                <?php echo e(__('tenders.quote_view_current_version')); ?>

            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e($priceQuote->number); ?></h1>
        <p class="page-subtitle"><?php echo e(__('tenders.price_quote')); ?> — <?php echo e(__('tenders.quote_version_label', ['version' => $priceQuote->version])); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <?php if($priceQuote->invoice_id): ?>
            <a href="<?php echo e(route('accounting.invoices.show', $priceQuote->invoice_id)); ?>" class="btn-primary">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <?php echo e(__('tenders.quote_view_invoice')); ?>

            </a>
        <?php elseif($priceQuote->is_current && $priceQuote->assigned_to === Auth::id()): ?>
            <form action="<?php echo e(route('price-quotes.convert-to-invoice', $priceQuote)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <?php echo e(__('tenders.quote_convert_to_invoice')); ?>

                </button>
            </form>
        <?php endif; ?>
        <a href="<?php echo e(route('price-quotes.print', ['priceQuote' => $priceQuote, 'lang' => 'ar'])); ?>" target="_blank" class="btn-secondary">
            <i class="fa-solid fa-print"></i>
            <?php echo e(__('tenders.quote_print_ar')); ?>

        </a>
        <a href="<?php echo e(route('price-quotes.print', ['priceQuote' => $priceQuote, 'lang' => 'en'])); ?>" target="_blank" class="btn-secondary">
            <i class="fa-solid fa-print"></i>
            <?php echo e(__('tenders.quote_print_en')); ?>

        </a>
        <?php if($priceQuote->is_current): ?>
            <a href="<?php echo e(route('price-quotes.edit', $priceQuote)); ?>" class="btn-secondary">
                <i class="fa-solid fa-pen"></i>
                <?php echo e(__('app.edit')); ?>

            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('price-quotes.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<?php if($priceQuote->is_current && $priceQuote->versions->isNotEmpty()): ?>
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.quote_version_history')); ?></h3>
    </div>
    <div class="px-6 py-4">
        <?php $__currentLoopData = $priceQuote->versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oldVersion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <span class="text-sm text-slate-600">
                    <?php echo e(__('tenders.quote_version_label', ['version' => $oldVersion->version])); ?>

                    — <span dir="ltr"><?php echo e($oldVersion->updated_at->format('Y-m-d H:i')); ?></span>
                </span>
                <a href="<?php echo e(route('price-quotes.show', $oldVersion)); ?>" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-eye"></i> <?php echo e(__('app.view')); ?>

                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.customer')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->customer?->localized_name); ?> (<?php echo e($priceQuote->customer?->code); ?>)</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_tender')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($priceQuote->tender): ?>
                    <a href="<?php echo e(route('tenders.show', $priceQuote->tender)); ?>" class="text-indigo-600 hover:underline"><?php echo e($priceQuote->tender->number); ?></a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->date->format('Y-m-d')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_branch')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->branch?->localized_name); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_currency')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->currency?->localized_name); ?> (<?php echo e($priceQuote->currency?->code); ?>)</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_validity_weeks')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->validity_weeks ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_supply_scope')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->supplyScope?->localized_name ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_delivery_term')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($priceQuote->deliveryTerm?->localized_name ?? '—'); ?></dd>
        </div>
        <div class="sm:col-span-3 flex flex-wrap gap-2">
            <span class="badge <?php echo e($priceQuote->winching_included ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'); ?>"><?php echo e(__('tenders.quote_winching_included')); ?>: <?php echo e($priceQuote->winching_included ? __('app.yes') : __('app.no')); ?></span>
            <span class="badge <?php echo e($priceQuote->sales_tax_included ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'); ?>"><?php echo e(__('tenders.quote_sales_tax_included')); ?>: <?php echo e($priceQuote->sales_tax_included ? __('app.yes') : __('app.no')); ?></span>
            <span class="badge <?php echo e($priceQuote->customs_fees_included ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'); ?>"><?php echo e(__('tenders.quote_customs_fees_included')); ?>: <?php echo e($priceQuote->customs_fees_included ? __('app.yes') : __('app.no')); ?></span>
        </div>
        <?php if($priceQuote->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.invoice_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($priceQuote->notes); ?></dd>
        </div>
        <?php endif; ?>
        <?php if($priceQuote->additional_terms): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_additional_terms')); ?></dt>
            <dd class="text-slate-700 whitespace-pre-line"><?php echo e($priceQuote->additional_terms); ?></dd>
        </div>
        <?php endif; ?>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.quote_assigned_to')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($priceQuote->invoice_id): ?>
                    <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('tenders.quote_status_invoiced')); ?></span>
                <?php elseif($priceQuote->assignee): ?>
                    <?php echo e($priceQuote->assignee->name); ?>

                    <span class="badge bg-violet-100 text-violet-700 ms-1"><?php echo e(__('tenders.quote_status_pending_invoice')); ?></span>
                <?php else: ?>
                    <span class="text-slate-400 font-normal"><?php echo e(__('tenders.quote_unassigned')); ?></span>
                <?php endif; ?>
            </dd>
        </div>
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.quote_items')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.material')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_total')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_notes')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $priceQuote->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800"><?php echo e($item->material?->localized_name); ?></span>
                        <span class="text-xs text-slate-400 ms-1"><?php echo e($item->material?->unit?->symbol); ?></span>
                    </td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->quantity, 3)); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->unit_price, 3)); ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?php echo e(number_format($item->total, 3)); ?></td>
                    <td class="px-5 py-3 text-xs text-slate-600">
                        <?php $__empty_1 = true; $__currentLoopData = $item->notes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <p>• <?php echo e($note); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-80">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium"><?php echo e(__('tenders.quote_subtotal')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e(number_format($priceQuote->subtotal, 3)); ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium">
                    <?php echo e(__('tenders.quote_discount')); ?>

                    <?php if($priceQuote->discount_type === 'percent' && $priceQuote->discount_amount > 0): ?>
                        <span class="text-xs text-slate-400" dir="ltr">(<?php echo e(rtrim(rtrim(number_format($priceQuote->discount_value, 3), '0'), '.')); ?>%)</span>
                    <?php endif; ?>
                </dt>
                <dd class="font-bold text-rose-600">- <?php echo e(number_format($priceQuote->discount_amount, 3)); ?></dd>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <dt class="text-slate-700 font-bold"><?php echo e(__('tenders.quote_total')); ?></dt>
                <dd class="font-black text-lg text-orange-700"><?php echo e(number_format($priceQuote->total, 3)); ?> <?php echo e($priceQuote->currency?->code); ?></dd>
            </div>
        </dl>
    </div>
</div>

<?php if($priceQuote->installments->isNotEmpty()): ?>
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-calendar-check text-orange-500 text-sm"></i>
            <?php echo e(__('tenders.quote_installments')); ?>

        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.installment_description')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-36"><?php echo e(__('tenders.installment_percentage')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $priceQuote->installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-5 py-3 text-sm text-slate-700"><?php echo e($installment->description ?: '—'); ?></td>
                    <td class="px-5 py-3 text-sm font-bold text-slate-800" dir="ltr"><?php echo e(number_format($installment->percentage, 2)); ?>%</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-quotes/show.blade.php ENDPATH**/ ?>