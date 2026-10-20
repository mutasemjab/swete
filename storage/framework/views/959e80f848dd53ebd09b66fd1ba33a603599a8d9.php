<?php $__env->startSection('title', __('agreements.add_agreement')); ?>
<?php $__env->startSection('breadcrumb', __('agreements.add_agreement')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('agreements.add_agreement')); ?></h1>
    </div>
    <a href="<?php echo e($project ? route('projects.show', $project) : route('agreements.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route('agreements.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('agreements._form', ['agreement' => null, 'project' => $project, 'customers' => $customers, 'suppliers' => $suppliers], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('agreements.add_agreement')); ?>

        </button>
        <a href="<?php echo e($project ? route('projects.show', $project) : route('agreements.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/agreements/create.blade.php ENDPATH**/ ?>