<?php $__env->startSection('title', __('customer_portal.dashboard_title')); ?>

<?php $__env->startSection('bar'); ?>
<div class="cp-bar">
    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>
    <div class="flex-1">
        <p class="font-black leading-tight"><?php echo e($customer->localized_name); ?></p>
        <p class="text-xs text-white/60"><?php echo e(__('customer_portal.dashboard_title')); ?></p>
    </div>
    <form method="POST" action="<?php echo e(route('customer-portal.logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 active:bg-white/20" title="<?php echo e(__('app.logout')); ?>">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if($visitsAwaitingReview->isNotEmpty()): ?>
<div class="cp-card !bg-amber-50 !border-amber-200">
    <p class="font-black text-amber-800 mb-3">
        <i class="fa-solid fa-signature"></i>
        <?php echo e(__('customer_portal.visits_awaiting_review')); ?>

    </p>
    <div class="space-y-2">
        <?php $__currentLoopData = $visitsAwaitingReview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('customer-portal.visits.show', $visit)); ?>" class="flex items-center justify-between bg-white rounded-xl px-4 py-3 hover:shadow-sm transition-all">
                <span class="font-semibold text-slate-700" dir="ltr"><?php echo e($visit->check_in_at->format('Y-m-d')); ?></span>
                <span class="cp-btn-primary !px-3 !py-1.5 !text-xs"><?php echo e(__('customer_portal.review_and_sign')); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<div class="cp-card">
    <div class="flex items-center justify-between mb-4">
        <p class="font-black text-slate-800"><?php echo e(__('customer_portal.my_contracts')); ?></p>
    </div>
    <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('customer-portal.contracts.show', $contract)); ?>" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/50 -mx-2 px-2 rounded-lg transition-colors">
            <div>
                <p class="font-bold text-slate-800" dir="ltr"><?php echo e($contract->number); ?></p>
                <p class="text-xs text-slate-400" dir="ltr"><?php echo e($contract->signed_date->format('Y-m-d')); ?> → <?php echo e($contract->expiry_date->format('Y-m-d')); ?></p>
            </div>
            <i class="fa-solid fa-chevron-<?php echo e(app()->isLocale('ar') ? 'left' : 'right'); ?> text-slate-300"></i>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-slate-400"><?php echo e(__('customer_portal.no_contracts')); ?></p>
    <?php endif; ?>
</div>

<div class="cp-card">
    <div class="flex items-center justify-between mb-4">
        <p class="font-black text-slate-800"><?php echo e(__('customer_portal.my_requests')); ?></p>
        <a href="<?php echo e(route('customer-portal.maintenance-requests.create')); ?>" class="cp-btn-primary !px-4 !py-2 !text-xs">
            <i class="fa-solid fa-plus"></i>
            <?php echo e(__('customer_portal.new_request')); ?>

        </a>
    </div>
    <?php $__empty_1 = true; $__currentLoopData = $maintenanceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maintenanceRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
            <div>
                <p class="text-sm text-slate-700"><?php echo e(\Illuminate\Support\Str::limit($maintenanceRequest->description, 70)); ?></p>
                <p class="text-xs text-slate-400"><?php echo e($maintenanceRequest->created_at->diffForHumans()); ?></p>
            </div>
            <?php if($maintenanceRequest->status === 'pending'): ?>
                <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('maintenance.request_status_pending')); ?></span>
            <?php elseif($maintenanceRequest->status === 'approved'): ?>
                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('maintenance.request_status_approved')); ?></span>
            <?php else: ?>
                <span class="badge bg-rose-100 text-rose-700"><?php echo e(__('maintenance.request_status_rejected')); ?></span>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-slate-400"><?php echo e(__('customer_portal.no_requests')); ?></p>
    <?php endif; ?>
</div>

<?php if($pastVisits->isNotEmpty()): ?>
<div class="cp-card">
    <p class="font-black text-slate-800 mb-4"><?php echo e(__('customer_portal.past_visits')); ?></p>
    <?php $__currentLoopData = $pastVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('customer-portal.visits.show', $visit)); ?>" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/50 -mx-2 px-2 rounded-lg transition-colors">
            <span class="text-sm text-slate-700" dir="ltr"><?php echo e($visit->check_in_at->format('Y-m-d')); ?></span>
            <?php echo $__env->make('maintenance.visits._status-badge', ['status' => $visit->status], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer-portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/customer-portal/dashboard.blade.php ENDPATH**/ ?>