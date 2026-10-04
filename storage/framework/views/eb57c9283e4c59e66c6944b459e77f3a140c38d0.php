<?php $__env->startSection('title', __('warehouse.material_details')); ?>
<?php $__env->startSection('breadcrumb', $material->localized_name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e($material->localized_name); ?></h1>
        <p class="page-subtitle"><?php echo e(__('warehouse.material_details')); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo e(route('warehouse.materials.edit', $material)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <a href="<?php echo e(route('warehouse.materials.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-4"><?php echo e(__('warehouse.material_details')); ?></h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('warehouse.material_code')); ?></dt>
                <dd class="font-bold text-slate-800 font-mono"><?php echo e($material->code); ?></dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('warehouse.material_category')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e($material->category?->localized_name); ?></dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('warehouse.material_unit')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e($material->unit?->localized_name); ?></dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('warehouse.min_stock_level')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e($material->min_stock_level ?? '—'); ?></dd>
            </div>
            <?php if($material->description): ?>
            <div class="sm:col-span-2">
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('warehouse.material_description')); ?></dt>
                <dd class="text-slate-700"><?php echo e($material->description); ?></dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="card px-6 py-5">
        <h3 class="text-sm font-black text-slate-700 mb-1"><?php echo e(__('warehouse.total_stock')); ?></h3>
        <p class="text-3xl font-black text-emerald-600 mb-4"><?php echo e(number_format($material->stocks->sum('quantity'), 3)); ?></p>

        <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2"><?php echo e(__('warehouse.stock_by_warehouse')); ?></h4>
        <?php if($material->stocks->isEmpty()): ?>
            <p class="text-sm text-slate-400">—</p>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $material->stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center justify-between py-2 text-sm">
                        <span class="text-slate-600"><?php echo e($stock->warehouse?->localized_name); ?></span>
                        <span class="font-bold text-slate-800"><?php echo e(number_format($stock->quantity, 3)); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/warehouse/materials/show.blade.php ENDPATH**/ ?>