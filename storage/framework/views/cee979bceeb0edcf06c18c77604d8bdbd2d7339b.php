<?php $__env->startSection('title', __('tenders.price_analyses_list')); ?>
<?php $__env->startSection('breadcrumb', __('tenders.price_analyses_list')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('tenders.price_analyses_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('tenders.price_analyses_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('price-analyses.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('tenders.add_price_analysis')); ?>

    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-chart-line text-orange-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($analyses->total()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('tenders.total_price_analyses')); ?></p>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route('price-analyses.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="branch_id" class="js-select2 form-select w-56">
        <option value=""><?php echo e(__('tenders.all_branches')); ?></option>
        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($branch->id); ?>" <?php if(request('branch_id') == $branch->id): echo 'selected'; endif; ?>><?php echo e($branch->localized_name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny(['branch_id'])): ?>
        <a href="<?php echo e(route('price-analyses.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden">
    <?php if($analyses->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-chart-line text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('tenders.no_price_analyses')); ?></p>
            <a href="<?php echo e(route('price-analyses.create')); ?>" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('tenders.add_first_price_analysis')); ?>

            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_branch')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_with_tax')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_subtotal')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $analyses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $analysis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('price-analyses.show', $analysis)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors"><?php echo e($analysis->number); ?></a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($analysis->branch?->localized_name); ?></td>
                        <td class="px-5 py-4">
                            <?php if($analysis->with_tax): ?>
                                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('app.yes')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-slate-100 text-slate-500"><?php echo e(__('app.no')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800" dir="ltr"><?php echo e(number_format($analysis->total_with_tax, 3)); ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('price-analyses.show', $analysis)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="<?php echo e(__('app.view')); ?>">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="<?php echo e(route('price-analyses.edit', $analysis)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route('price-analyses.destroy', $analysis)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
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

        <?php if($analyses->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($analyses->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($analyses->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($analyses->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($analyses->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($analyses->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($analyses->hasMorePages()): ?>
                    <a href="<?php echo e($analyses->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-analyses/index.blade.php ENDPATH**/ ?>