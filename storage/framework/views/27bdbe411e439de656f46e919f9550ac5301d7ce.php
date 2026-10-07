<?php $__env->startSection('title', __('maintenance.visits_list')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.visits_list')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.visits_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.visits_subtitle')); ?></p>
    </div>
</div>

<div class="card mb-5 px-5 py-4">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value=""><?php echo e(__('app.all')); ?></option>
            <option value="open" <?php if(request('status') === 'open'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.visit_status_open')); ?></option>
            <option value="submitted_to_customer" <?php if(request('status') === 'submitted_to_customer'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.visit_status_submitted')); ?></option>
            <option value="customer_signed" <?php if(request('status') === 'customer_signed'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.visit_status_signed')); ?></option>
            <option value="closed" <?php if(request('status') === 'closed'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.visit_status_closed')); ?></option>
        </select>
    </form>
</div>

<div class="card overflow-hidden">
    <?php if($visits->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-route text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('maintenance.no_visits')); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.report_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.visit_technician')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.visit_duration')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.visit_type')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-800">
                            <a href="<?php echo e(route('maintenance-visits.show', $visit)); ?>" class="hover:text-indigo-600 hover:underline"><?php echo e($visit->customer?->localized_name); ?></a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($visit->technician?->name); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-500" dir="ltr"><?php echo e($visit->duration ?? '—'); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($visit->visitType?->localized_name ?? '—'); ?></td>
                        <td class="px-5 py-4">
                            <?php echo $__env->make('maintenance.visits._status-badge', ['status' => $visit->status], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </td>
                        <td class="px-5 py-4 text-end">
                            <a href="<?php echo e(route('maintenance-visits.show', $visit)); ?>" class="btn-secondary btn-sm">
                                <i class="fa-solid fa-eye"></i> <?php echo e(__('app.view')); ?>

                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><?php echo e($visits->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/visits/index.blade.php ENDPATH**/ ?>