<?php $__env->startSection('title', __('tenders.reminder')); ?>
<?php $__env->startSection('breadcrumb', __('tenders.reminder')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e(__('tenders.reminder')); ?>

            <?php if($reminder->status === 'fulfilled'): ?>
                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('tenders.reminder_status_fulfilled')); ?></span>
            <?php else: ?>
                <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('tenders.reminder_status_pending')); ?></span>
            <?php endif; ?>
        </h1>
        <p class="page-subtitle"><?php echo e($reminder->project?->number); ?> — <?php echo e($reminder->project?->localized_title); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <?php if($reminder->status === 'pending'): ?>
            <a href="<?php echo e(route('purchase-requests.create', ['reminder_id' => $reminder->id])); ?>" class="btn-primary">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('tenders.reminder_create_pr')); ?>

            </a>
        <?php else: ?>
            <a href="<?php echo e(route('purchase-requests.show', $reminder->purchase_request_id)); ?>" class="btn-primary">
                <i class="fa-solid fa-eye"></i>
                <?php echo e(__('tenders.reminder_view_pr')); ?>

            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('purchase-request-reminders.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project')); ?></dt>
            <dd class="font-bold text-slate-800">
                <a href="<?php echo e(route('projects.show', $reminder->project)); ?>" class="text-indigo-600 hover:underline"><?php echo e($reminder->project?->number); ?></a>
                — <?php echo e($reminder->project?->localized_title); ?>

            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_customer')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($reminder->project?->customer?->localized_name ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('approvals.requested_by')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($reminder->requester?->name); ?> — <?php echo e($reminder->created_at->format('Y-m-d')); ?></dd>
        </div>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.reminder_drive_url')); ?></dt>
            <dd class="font-bold text-slate-800">
                <a href="<?php echo e($reminder->google_drive_url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all">
                    <i class="fa-brands fa-google-drive"></i> <?php echo e($reminder->google_drive_url); ?>

                </a>
            </dd>
        </div>
        <?php if($reminder->status === 'fulfilled'): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.reminder_fulfilled_by')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($reminder->fulfiller?->name); ?> — <?php echo e($reminder->fulfilled_at?->format('Y-m-d H:i')); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.reminder_items')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.material')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.item_ercd')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_total')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.item_features')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $reminder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800"><?php echo e($item->material?->localized_name); ?></span>
                        <span class="text-xs text-slate-400 ms-1"><?php echo e($item->material?->unit?->symbol); ?></span>
                    </td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->quantity, 3)); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e($item->ercd ?? '—'); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e($item->unit_price !== null ? number_format($item->unit_price, 3) : '—'); ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?php echo e($item->total !== null ? number_format($item->total, 3) : '—'); ?></td>
                    <td class="px-5 py-3 text-slate-600 text-xs">
                        <?php $__empty_1 = true; $__currentLoopData = $item->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="badge bg-slate-100 text-slate-600 me-1 mb-1"><?php echo e($feature->value); ?></span>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/purchase-request-reminders/show.blade.php ENDPATH**/ ?>