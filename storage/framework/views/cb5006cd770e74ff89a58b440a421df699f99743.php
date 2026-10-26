<?php $__env->startSection('title', __('tenders.edit_price_analysis')); ?>
<?php $__env->startSection('breadcrumb', __('tenders.edit_price_analysis')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('tenders.edit_price_analysis')); ?></h1>
        <p class="page-subtitle"><?php echo e($analysis->number); ?></p>
    </div>
    <a href="<?php echo e(route('price-analyses.show', $analysis)); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route('price-analyses.update', $analysis)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('tenders.price-analyses._form', ['analysis' => $analysis, 'branches' => $branches, 'ciatDiscounts' => $ciatDiscounts], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('app.save')); ?>

        </button>
        <a href="<?php echo e(route('price-analyses.show', $analysis)); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-analyses/edit.blade.php ENDPATH**/ ?>