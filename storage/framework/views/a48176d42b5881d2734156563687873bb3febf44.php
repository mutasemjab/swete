<?php $__env->startSection('title', __('maintenance.contract_scheduled_visits')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.contract_scheduled_visits')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.contract_scheduled_visits')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.contracts_subtitle')); ?></p>
    </div>
    <?php if($dueCount > 0): ?>
        <a href="<?php echo e(route('contract-scheduled-visits.index', ['due' => 1])); ?>" class="badge bg-rose-100 text-rose-700 !text-sm !px-4 !py-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php echo e(__('maintenance.scheduled_visits_due_count', ['count' => $dueCount])); ?>

        </a>
    <?php endif; ?>
</div>

<div class="card overflow-hidden">
    <?php if($scheduledVisits->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-calendar-days text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('maintenance.no_scheduled_visits')); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.contract_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.contract_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.scheduled_visit_date')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.scheduled_visit_type')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $scheduledVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheduledVisit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors <?php if($scheduledVisit->isOverdue()): ?> bg-rose-50/60 <?php elseif($scheduledVisit->isDueToday()): ?> bg-amber-50/60 <?php endif; ?>">
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('maintenance-contracts.show', $scheduledVisit->contract)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors"><?php echo e($scheduledVisit->contract?->number); ?></a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($scheduledVisit->contract?->customer?->localized_name); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            <span dir="ltr"><?php echo e($scheduledVisit->scheduled_date->format('Y-m-d')); ?></span>
                            <?php if($scheduledVisit->isOverdue()): ?>
                                <span class="badge bg-rose-100 text-rose-700 ms-1.5"><?php echo e(__('maintenance.visit_overdue')); ?></span>
                            <?php elseif($scheduledVisit->isDueToday()): ?>
                                <span class="badge bg-amber-100 text-amber-700 ms-1.5"><?php echo e(__('maintenance.visit_due_today')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge <?php echo e($scheduledVisit->type === 'emergency' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700'); ?>">
                                <?php echo e(__('maintenance.scheduled_visit_type_' . $scheduledVisit->type)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><?php echo e($scheduledVisits->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/contracts/scheduled-visits-index.blade.php ENDPATH**/ ?>