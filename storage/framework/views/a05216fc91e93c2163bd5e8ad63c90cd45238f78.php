<?php $__env->startSection('title', $contract->number); ?>

<?php $__env->startSection('bar'); ?>
<div class="cp-bar">
    <a href="<?php echo e(route('customer-portal.dashboard')); ?>" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    </a>
    <div class="flex-1">
        <p class="font-black leading-tight" dir="ltr"><?php echo e($contract->number); ?></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="cp-card">
    <dl class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.contract_signed_date')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e($contract->signed_date->format('Y-m-d')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.contract_expiry_date')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e($contract->expiry_date->format('Y-m-d')); ?></dd>
        </div>
    </dl>
</div>

<div class="cp-card">
    <p class="font-black text-slate-800 mb-3"><?php echo e(__('maintenance.contract_payments')); ?></p>
    <?php $__empty_1 = true; $__currentLoopData = $contract->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
            <span class="text-sm text-slate-600" dir="ltr"><?php echo e($payment->due_date->format('Y-m-d')); ?></span>
            <span class="font-bold text-slate-800" dir="ltr"><?php echo e(number_format($payment->amount, 3)); ?> <?php echo e($payment->currency?->code); ?></span>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-slate-400"><?php echo e(__('maintenance.no_payments')); ?></p>
    <?php endif; ?>
</div>

<div class="cp-card">
    <p class="font-black text-slate-800 mb-3"><?php echo e(__('maintenance.contract_scheduled_visits')); ?></p>
    <?php $__empty_1 = true; $__currentLoopData = $contract->scheduledVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheduledVisit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
            <span class="text-sm text-slate-600" dir="ltr"><?php echo e($scheduledVisit->scheduled_date->format('Y-m-d')); ?></span>
            <span class="badge <?php echo e($scheduledVisit->type === 'emergency' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700'); ?>">
                <?php echo e(__('maintenance.scheduled_visit_type_' . $scheduledVisit->type)); ?>

            </span>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-slate-400"><?php echo e(__('maintenance.no_scheduled_visits')); ?></p>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer-portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/customer-portal/contracts/show.blade.php ENDPATH**/ ?>