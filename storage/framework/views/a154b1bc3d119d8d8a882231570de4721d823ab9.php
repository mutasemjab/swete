<?php $__env->startSection('title', __('maintenance.reports_list')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.reports_list')); ?>

<?php $__env->startSection('content'); ?>
<?php $filterKeys = ['customer_id', 'template_id']; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.reports_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.reports_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('maintenance-reports.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('maintenance.add_report')); ?>

    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clipboard-list text-teal-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($reports->total()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('maintenance.total_reports')); ?></p>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route('maintenance-reports.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="customer_id" class="js-select2 form-select w-56">
        <option value=""><?php echo e(__('maintenance.all_customers')); ?></option>
        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($customer->id); ?>" <?php if(request('customer_id') == $customer->id): echo 'selected'; endif; ?>><?php echo e($customer->localized_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="template_id" class="js-select2 form-select w-56">
        <option value=""><?php echo e(__('maintenance.all_templates')); ?></option>
        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($template->id); ?>" <?php if(request('template_id') == $template->id): echo 'selected'; endif; ?>><?php echo e($template->localized_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny($filterKeys)): ?>
        <a href="<?php echo e(route('maintenance-reports.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden">
    <?php if($reports->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-clipboard-list text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                <?php echo e(request()->hasAny($filterKeys) ? __('maintenance.no_reports_search') : __('maintenance.no_reports')); ?>

            </p>
            <?php if(!request()->hasAny($filterKeys)): ?>
                <a href="<?php echo e(route('maintenance-reports.create')); ?>" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    <?php echo e(__('maintenance.add_first_report')); ?>

                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.report_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.report_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('maintenance.report_product')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.report_date')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('maintenance-reports.show', $report)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors"><?php echo e($report->number); ?></a>
                            <p class="text-xs text-slate-400"><?php echo e($report->template_name); ?></p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($report->customer?->localized_name); ?></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600"><?php echo e($report->material?->localized_name); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($report->date->format('Y-m-d')); ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('maintenance-reports.show', $report)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="<?php echo e(__('app.view')); ?>">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="<?php echo e(route('maintenance-reports.edit', $report)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route('maintenance-reports.destroy', $report)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($reports->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($reports->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($reports->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($reports->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($reports->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($reports->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($reports->hasMorePages()): ?>
                    <a href="<?php echo e($reports->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/reports/index.blade.php ENDPATH**/ ?>