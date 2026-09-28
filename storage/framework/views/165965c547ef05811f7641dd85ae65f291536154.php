<?php $__env->startSection('title', $report->number); ?>
<?php $__env->startSection('breadcrumb', $report->number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e($report->number); ?></h1>
        <p class="page-subtitle"><?php echo e($report->template_name); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <?php if($report->price_quote_id): ?>
            <a href="<?php echo e(route('price-quotes.show', $report->price_quote_id)); ?>" class="btn-secondary">
                <i class="fa-solid fa-file-invoice"></i>
                <?php echo e($report->priceQuote?->number); ?>

            </a>
        <?php else: ?>
            <form action="<?php echo e(route('maintenance-reports.convert-to-quote', $report)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-secondary" title="<?php echo e(__('maintenance.convert_to_quote_hint')); ?>">
                    <i class="fa-solid fa-file-invoice"></i>
                    <?php echo e(__('maintenance.convert_to_quote')); ?>

                </button>
            </form>
        <?php endif; ?>
        <a href="<?php echo e(route('maintenance-reports.edit', $report)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <a href="<?php echo e(route('maintenance-reports.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_customer')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($report->customer?->localized_name); ?> (<?php echo e($report->customer?->code); ?>)</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_product')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($report->material?->localized_name ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($report->date->format('Y-m-d')); ?></dd>
        </div>
        <?php if($report->problem): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_problem')); ?></dt>
            <dd class="text-slate-700"><?php echo e($report->problem); ?></dd>
        </div>
        <?php endif; ?>
        <?php if($report->solution): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_solution')); ?></dt>
            <dd class="text-slate-700"><?php echo e($report->solution); ?></dd>
        </div>
        <?php endif; ?>
        <?php if($report->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($report->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('maintenance.template_fields')); ?></h3>
    </div>
    <div class="divide-y divide-slate-100">
        <?php $__currentLoopData = $report->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <dt class="text-slate-500 font-medium sm:col-span-2"><?php echo e($field->localized_question); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($field->type === 'images'): ?>
                    <?php if($field->image_urls): ?>
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $field->image_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($url); ?>" target="_blank"><img src="<?php echo e($url); ?>" class="w-20 h-20 object-cover rounded-lg border border-slate-200"></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <span class="font-normal text-slate-400"><?php echo e(__('maintenance.report_no_images')); ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo e($field->formatted_answer ?? __('maintenance.answer_not_answered')); ?>

                <?php endif; ?>
            </dd>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-teal-500 text-sm"></i>
            <?php echo e(__('maintenance.report_materials_used')); ?>

        </h3>
        <?php if($report->materials_approval_status !== 'none'): ?>
            <span class="badge
                <?php if($report->materials_approval_status === 'approved'): ?> bg-emerald-100 text-emerald-700
                <?php elseif($report->materials_approval_status === 'rejected'): ?> bg-rose-100 text-rose-700
                <?php else: ?> bg-amber-100 text-amber-700 <?php endif; ?>">
                <?php echo e(__('maintenance.materials_approval_status_' . $report->materials_approval_status)); ?>

            </span>
        <?php endif; ?>
    </div>
    <div class="px-6 py-5">
        <?php if($report->materials->isEmpty()): ?>
            <p class="text-sm text-slate-400"><?php echo e(__('maintenance.report_no_materials')); ?></p>
        <?php else: ?>
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="text-start text-xs font-black text-slate-500 uppercase tracking-wider">
                        <th class="text-start py-1.5"><?php echo e(__('maintenance.report_material')); ?></th>
                        <th class="text-start py-1.5"><?php echo e(__('maintenance.report_quantity')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $report->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="py-2 text-slate-700"><?php echo e($item->material?->localized_name); ?> (<?php echo e($item->material?->code); ?>)</td>
                        <td class="py-2 font-bold text-slate-800" dir="ltr"><?php echo e(number_format($item->quantity, 3)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <?php if($report->issueVoucher): ?>
                <a href="<?php echo e(route('warehouse.vouchers.show', ['type' => 'issue', 'voucher' => $report->issue_voucher_id])); ?>" class="text-sm text-indigo-600 hover:underline">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                    <?php echo e(__('maintenance.view_issue_voucher')); ?>

                </a>
            <?php endif; ?>

            <?php if($report->materialApprovals->isNotEmpty()): ?>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-500 mb-2"><?php echo e(__('maintenance.materials_approvers')); ?></p>
                    <ul class="space-y-1.5">
                        <?php $__currentLoopData = $report->materialApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-slate-700"><?php echo e($approval->user?->name); ?></span>
                            <?php if($approval->decision === 'pending'): ?>
                                <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('maintenance.materials_approval_status_pending')); ?></span>
                            <?php elseif($approval->decision === 'approved'): ?>
                                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('maintenance.materials_approval_status_approved')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-rose-100 text-rose-700" title="<?php echo e($approval->note); ?>"><?php echo e(__('maintenance.materials_approval_status_rejected')); ?></span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>

                    <?php $myApproval = $report->materialApprovals->firstWhere('user_id', Auth::id()); ?>
                    <?php if($myApproval && $myApproval->decision === 'pending'): ?>
                        <div class="flex items-center gap-2 mt-4" x-data="{ showReject: false, note: '' }">
                            <form action="<?php echo e(route('maintenance-reports.approve-materials', $report)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-primary btn-sm">
                                    <i class="fa-solid fa-check"></i> <?php echo e(__('maintenance.approve_materials')); ?>

                                </button>
                            </form>
                            <button type="button" @click="showReject = true" class="btn-danger btn-sm" x-show="!showReject">
                                <i class="fa-solid fa-xmark"></i> <?php echo e(__('maintenance.reject_materials')); ?>

                            </button>
                            <form action="<?php echo e(route('maintenance-reports.reject-materials', $report)); ?>" method="POST" class="flex items-center gap-2" x-show="showReject" x-cloak>
                                <?php echo csrf_field(); ?>
                                <input type="text" name="note" x-model="note" class="form-input !py-1.5 !text-sm" placeholder="<?php echo e(__('maintenance.reject_materials_note')); ?>">
                                <button type="submit" class="btn-danger btn-sm"><?php echo e(__('maintenance.reject_materials')); ?></button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/reports/show.blade.php ENDPATH**/ ?>