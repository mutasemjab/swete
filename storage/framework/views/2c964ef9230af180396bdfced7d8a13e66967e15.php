<?php $__env->startSection('title', $invoice->number); ?>
<?php $__env->startSection('breadcrumb', $invoice->number); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusStyles = ['draft' => 'bg-amber-100 text-amber-700', 'posted' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-slate-100 text-slate-500'];
?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($invoice->number); ?>

            <span class="badge <?php echo e($statusStyles[$invoice->status]); ?>"><?php echo e(__('accounting.invoice_status_' . $invoice->status)); ?></span>
        </h1>
        <p class="page-subtitle"><?php echo e($invoice->invoiceType?->localized_name); ?></p>
    </div>
    <a href="<?php echo e(route('accounting.invoices.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.invoice_party')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($invoice->party?->localized_name); ?> <span class="text-slate-400 font-normal">(<?php echo e($invoice->party?->code); ?>)</span></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.invoice_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($invoice->date->format('Y-m-d')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.invoice_due_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($invoice->due_date?->format('Y-m-d') ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.invoice_currency')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($invoice->currency?->localized_name ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('settings.user')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($invoice->creator?->name); ?></dd>
        </div>
        <?php if($invoice->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('accounting.invoice_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($invoice->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('accounting.invoice_items')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_description')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_quantity')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_total')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-5 py-3 text-slate-700"><?php echo e($item->description); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->quantity, 3)); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->unit_price, 3)); ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?php echo e(number_format($item->total, 3)); ?></td>
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
                <dt class="text-slate-500"><?php echo e(__('accounting.invoice_subtotal')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e(number_format($invoice->subtotal, 3)); ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500"><?php echo e(__('accounting.invoice_tax_total')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e(number_format($invoice->tax_total, 3)); ?></dd>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-100">
                <dt class="text-slate-700 font-bold"><?php echo e(__('accounting.invoice_total')); ?></dt>
                <dd class="font-black text-lg text-blue-700"><?php echo e(number_format($invoice->total, 3)); ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-emerald-600"><?php echo e(__('accounting.invoice_paid_amount')); ?></dt>
                <dd class="font-bold text-emerald-700"><?php echo e(number_format($invoice->paid_amount, 3)); ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-rose-600"><?php echo e(__('accounting.invoice_balance_due')); ?></dt>
                <dd class="font-bold text-rose-700"><?php echo e(number_format($invoice->balance_due, 3)); ?></dd>
            </div>
        </dl>
    </div>
</div>

<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('accounting.receipt_vouchers_section')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $invoice->receipts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs text-slate-500"><?php echo e($receipt->number); ?></span>
                    <span class="text-sm text-slate-600" dir="ltr"><?php echo e($receipt->date->format('Y-m-d')); ?></span>
                    <?php if($receipt->notes): ?>
                        <span class="text-xs text-slate-400"><?php echo e($receipt->notes); ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-bold text-emerald-700" dir="ltr"><?php echo e(number_format($receipt->amount, 3)); ?></span>
                    <form action="<?php echo e(route('accounting.invoices.receipts.destroy', [$invoice, $receipt])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('accounting.no_receipt_vouchers')); ?></p>
        <?php endif; ?>

        <form action="<?php echo e(route('accounting.invoices.receipts.store', $invoice)); ?>" method="POST"
              class="mt-4 pt-4 border-t border-slate-100 flex items-end gap-3 flex-wrap">
            <?php echo csrf_field(); ?>
            <div class="flex-1 min-w-36">
                <label class="form-label"><?php echo e(__('accounting.receipt_voucher_date')); ?></label>
                <input type="date" name="date" value="<?php echo e(now()->toDateString()); ?>" class="form-input">
            </div>
            <div class="flex-1 min-w-36">
                <label class="form-label"><?php echo e(__('accounting.receipt_voucher_amount')); ?></label>
                <input type="number" name="amount" step="0.001" min="0" dir="ltr" class="form-input">
            </div>
            <div class="flex-1 min-w-48">
                <label class="form-label"><?php echo e(__('accounting.receipt_voucher_notes')); ?></label>
                <input type="text" name="notes" class="form-input">
            </div>
            <button type="submit" class="btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('accounting.add_receipt_voucher')); ?>

            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/accounting/invoices/show.blade.php ENDPATH**/ ?>