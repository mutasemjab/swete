<?php $__env->startSection('title', __('tenders.add_price_analysis')); ?>
<?php $__env->startSection('breadcrumb', __('tenders.add_price_analysis')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('tenders.add_price_analysis')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('tenders.add_price_analysis_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('price-analyses.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<?php if($ciatDiscounts->isEmpty()): ?>
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-800 font-bold text-lg mb-4"><?php echo e(__('tenders.no_ciat_discounts_available')); ?></p>
        <a href="<?php echo e(route('ciat-discounts.create')); ?>" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <?php echo e(__('tenders.add_ciat_discount')); ?>

        </a>
    </div>
<?php else: ?>
<form action="<?php echo e(route('price-analyses.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('tenders.price-analyses._form', ['analysis' => null, 'branches' => $branches, 'ciatDiscounts' => $ciatDiscounts], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('tenders.add_price_analysis')); ?>

        </button>
        <a href="<?php echo e(route('price-analyses.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-analyses/create.blade.php ENDPATH**/ ?>