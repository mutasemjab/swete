<?php $__env->startSection('title', $contract->number); ?>
<?php $__env->startSection('breadcrumb', $contract->number); ?>

<?php $__env->startSection('content'); ?>
<?php
    $expiringSoon = $contract->isExpiringSoon();
    $expired      = $contract->isExpired();
?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($contract->number); ?>

            <?php if($expiringSoon): ?>
                <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('maintenance.contract_expiring_soon')); ?></span>
            <?php elseif($expired): ?>
                <span class="badge bg-rose-100 text-rose-700"><?php echo e(__('maintenance.contract_expired')); ?></span>
            <?php endif; ?>
        </h1>
        <p class="page-subtitle"><?php echo e($contract->customer?->localized_name); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo e($contract->url); ?>" target="_blank" rel="noopener" class="btn-secondary">
            <i class="fa-solid fa-file-pdf"></i>
            <?php echo e(__('maintenance.view_contract_file')); ?>

        </a>
        <a href="<?php echo e(route('maintenance-contracts.edit', $contract)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <a href="<?php echo e(route('maintenance-contracts.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.contract_customer')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($contract->customer?->localized_name); ?> (<?php echo e($contract->customer?->code); ?>)</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.contract_signed_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($contract->signed_date->format('Y-m-d')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.contract_expiry_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($contract->expiry_date->format('Y-m-d')); ?></dd>
        </div>
        <?php if($contract->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.contract_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($contract->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('maintenance.contract_payments')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $contract->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between flex-wrap gap-2 py-2.5 border-b border-slate-100 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-sm text-slate-500" dir="ltr"><?php echo e($payment->due_date->format('Y-m-d')); ?></span>
                    <span class="font-bold text-slate-800" dir="ltr"><?php echo e(number_format($payment->amount, 3)); ?> <?php echo e($payment->currency?->code); ?></span>
                    <?php if($payment->notes): ?>
                        <span class="text-xs text-slate-400"><?php echo e($payment->notes); ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-2">
                    <?php if($payment->invoice_id): ?>
                        <a href="<?php echo e(route('accounting.invoices.show', $payment->invoice_id)); ?>" class="badge bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors">
                            <?php echo e(__('maintenance.payment_status_invoiced')); ?>

                        </a>
                    <?php elseif($payment->assignee): ?>
                        <span class="badge bg-violet-100 text-violet-700"><?php echo e($payment->assignee->name); ?></span>
                    <?php else: ?>
                        <span class="text-xs text-slate-400"><?php echo e(__('maintenance.payment_unassigned')); ?></span>
                    <?php endif; ?>
                    <?php if(! $payment->invoice_id): ?>
                        <form action="<?php echo e(route('contract-payments.destroy', [$contract, $payment])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('maintenance.no_payments')); ?></p>
        <?php endif; ?>

        <form action="<?php echo e(route('contract-payments.store', $contract)); ?>" method="POST" class="flex items-end gap-3 flex-wrap mt-4 pt-4 border-t border-slate-100">
            <?php echo csrf_field(); ?>
            <div class="min-w-40">
                <label class="form-label"><?php echo e(__('maintenance.payment_due_date')); ?></label>
                <input type="date" name="due_date" required class="form-input">
            </div>
            <div class="min-w-32">
                <label class="form-label"><?php echo e(__('maintenance.payment_amount')); ?></label>
                <input type="number" step="0.001" min="0.001" name="amount" dir="ltr" required class="form-input">
            </div>
            <div class="min-w-40">
                <label class="form-label"><?php echo e(__('maintenance.payment_currency')); ?></label>
                <select name="currency_id" class="js-select2 form-select" required>
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($currency->id); ?>" <?php if($currency->is_default): echo 'selected'; endif; ?>><?php echo e($currency->localized_name); ?> (<?php echo e($currency->code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-1 min-w-40">
                <label class="form-label"><?php echo e(__('maintenance.payment_notes')); ?></label>
                <input type="text" name="notes" class="form-input">
            </div>
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('maintenance.add_payment')); ?>

            </button>
        </form>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('maintenance.contract_scheduled_visits')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $contract->scheduledVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheduledVisit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between flex-wrap gap-2 py-2.5 border-b border-slate-100 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-sm text-slate-500" dir="ltr"><?php echo e($scheduledVisit->scheduled_date->format('Y-m-d')); ?></span>
                    <span class="badge <?php echo e($scheduledVisit->type === 'emergency' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700'); ?>">
                        <?php echo e(__('maintenance.scheduled_visit_type_' . $scheduledVisit->type)); ?>

                    </span>
                    <?php if($scheduledVisit->isOverdue()): ?>
                        <span class="badge bg-rose-100 text-rose-700"><?php echo e(__('maintenance.visit_overdue')); ?></span>
                    <?php elseif($scheduledVisit->isDueToday()): ?>
                        <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('maintenance.visit_due_today')); ?></span>
                    <?php endif; ?>
                    <?php if($scheduledVisit->notes): ?>
                        <span class="text-xs text-slate-400"><?php echo e($scheduledVisit->notes); ?></span>
                    <?php endif; ?>
                </div>
                <form action="<?php echo e(route('contract-scheduled-visits.destroy', [$contract, $scheduledVisit])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('maintenance.no_scheduled_visits')); ?></p>
        <?php endif; ?>

        <form action="<?php echo e(route('contract-scheduled-visits.store', $contract)); ?>" method="POST" class="flex items-end gap-3 flex-wrap mt-4 pt-4 border-t border-slate-100">
            <?php echo csrf_field(); ?>
            <div class="min-w-40">
                <label class="form-label"><?php echo e(__('maintenance.scheduled_visit_date')); ?></label>
                <input type="date" name="scheduled_date" required class="form-input">
            </div>
            <div class="min-w-40">
                <label class="form-label"><?php echo e(__('maintenance.scheduled_visit_type')); ?></label>
                <select name="type" class="form-select" required>
                    <option value="periodic"><?php echo e(__('maintenance.scheduled_visit_type_periodic')); ?></option>
                    <option value="emergency"><?php echo e(__('maintenance.scheduled_visit_type_emergency')); ?></option>
                </select>
            </div>
            <div class="flex-1 min-w-40">
                <label class="form-label"><?php echo e(__('maintenance.payment_notes')); ?></label>
                <input type="text" name="notes" class="form-input">
            </div>
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('maintenance.add_scheduled_visit')); ?>

            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/contracts/show.blade.php ENDPATH**/ ?>