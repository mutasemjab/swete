<?php $__env->startSection('title', $analysis->number); ?>
<?php $__env->startSection('breadcrumb', $analysis->number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e($analysis->number); ?></h1>
        <p class="page-subtitle"><?php echo e($analysis->branch?->localized_name); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo e(route('price-analyses.edit', $analysis)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <a href="<?php echo e(route('price-analyses.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.analysis_branch')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($analysis->branch?->localized_name); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.analysis_tax_rate')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e(rtrim(rtrim(number_format($analysis->tax_rate, 2), '0'), '.')); ?>%</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.analysis_jd_rate')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e(rtrim(rtrim(number_format($analysis->jd_rate, 4), '0'), '.')); ?></dd>
        </div>
        <?php if($analysis->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.analysis_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($analysis->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.analysis_item_product')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.ciat_type')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.ciat_model')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_quantity')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_list_price')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_discount')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_cost')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_profit')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_total_profit')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_price')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_to_jd')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_shipping')); ?></th>
                    <th class="px-3 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_item_subtotal')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $analysis->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-3 py-3 font-bold text-slate-800"><?php echo e($item->ciat_type); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e($item->ciat_model); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->quantity, 3)); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->list_price, 3)); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(rtrim(rtrim(number_format($item->discount_percent, 2), '0'), '.')); ?>%</td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->cost, 3)); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->profit, 3)); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->total_profit, 3)); ?></td>
                    <td class="px-3 py-3 font-bold text-slate-800" dir="ltr"><?php echo e(number_format($item->price, 3)); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->to_jd, 3)); ?></td>
                    <td class="px-3 py-3 text-slate-600" dir="ltr"><?php echo e(number_format($item->shipping, 3)); ?></td>
                    <td class="px-3 py-3 font-bold text-orange-700" dir="ltr"><?php echo e(number_format($item->subtotal, 3)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-96">
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-500 font-medium"><?php echo e(__('tenders.analysis_item_subtotal')); ?></dt>
                <dd class="font-bold text-slate-800" dir="ltr"><?php echo e(number_format($analysis->subtotal, 3)); ?></dd>
            </div>
            <?php if($analysis->with_tax): ?>
            <div class="flex justify-between pt-2 border-t border-slate-200">
                <dt class="text-slate-700 font-bold"><?php echo e(__('tenders.analysis_total_with_tax')); ?></dt>
                <dd class="font-black text-lg text-orange-700" dir="ltr"><?php echo e(number_format($analysis->total_with_tax, 3)); ?></dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-analyses/show.blade.php ENDPATH**/ ?>