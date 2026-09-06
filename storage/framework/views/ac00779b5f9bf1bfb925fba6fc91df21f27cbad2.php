<?php $__env->startSection('title', __('accounting.edit_group')); ?>
<?php $__env->startSection('breadcrumb', __('accounting.edit_group')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('accounting.edit_group')); ?></h1>
        <p class="page-subtitle"><?php echo e($group->localized_name); ?></p>
    </div>
    <a href="<?php echo e(route("accounting.{$type}-groups.index")); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route("accounting.{$type}-groups.update", $group)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('accounting.party-groups._form', ['parents' => $parents, 'group' => $group], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('app.save')); ?>

        </button>
        <a href="<?php echo e(route("accounting.{$type}-groups.index")); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/accounting/party-groups/edit.blade.php ENDPATH**/ ?>