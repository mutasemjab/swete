<?php $__env->startSection('title', __('maintenance.requests_list')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.requests_list')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.requests_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.requests_subtitle')); ?></p>
    </div>
</div>

<div class="card mb-5 px-5 py-4">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" class="form-select max-w-xs" onchange="this.form.submit()">
            <option value=""><?php echo e(__('app.all')); ?></option>
            <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.request_status_pending')); ?></option>
            <option value="approved" <?php if(request('status') === 'approved'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.request_status_approved')); ?></option>
            <option value="rejected" <?php if(request('status') === 'rejected'): echo 'selected'; endif; ?>><?php echo e(__('maintenance.request_status_rejected')); ?></option>
        </select>
    </form>
</div>

<div class="card overflow-hidden">
    <?php if($maintenanceRequests->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-bell text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('maintenance.no_requests')); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.report_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.request_description')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.request_preferred_date')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('approvals.requested_at')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $maintenanceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maintenanceRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-800"><?php echo e($maintenanceRequest->customer?->localized_name); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e(\Illuminate\Support\Str::limit($maintenanceRequest->description, 80)); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-500" dir="ltr"><?php echo e($maintenanceRequest->preferred_date?->format('Y-m-d') ?? '—'); ?></td>
                        <td class="px-5 py-4">
                            <?php if($maintenanceRequest->status === 'pending'): ?>
                                <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('maintenance.request_status_pending')); ?></span>
                            <?php elseif($maintenanceRequest->status === 'approved'): ?>
                                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('maintenance.request_status_approved')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-rose-100 text-rose-700"><?php echo e(__('maintenance.request_status_rejected')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500"><?php echo e($maintenanceRequest->created_at->diffForHumans()); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4"><?php echo e($maintenanceRequests->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/requests/index.blade.php ENDPATH**/ ?>