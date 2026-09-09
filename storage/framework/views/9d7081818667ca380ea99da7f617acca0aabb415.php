<?php $__env->startSection('title', __('external_purchases.add_purchase_request')); ?>
<?php $__env->startSection('breadcrumb', __('external_purchases.add_purchase_request')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('external_purchases.add_purchase_request')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('external_purchases.add_purchase_request_subtitle')); ?></p>
    </div>
    <a href="<?php echo e($project ? route('projects.show', $project) : route('purchase-requests.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route('purchase-requests.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('external-purchases.purchase-requests._form', [
        'projects' => $projects, 'serviceCalls' => $serviceCalls, 'suppliers' => $suppliers,
        'branches' => $branches, 'currencies' => $currencies, 'countries' => $countries,
        'materials' => $materials, 'project' => $project, 'serviceCall' => $serviceCall,
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('external_purchases.add_purchase_request')); ?>

        </button>
        <a href="<?php echo e($project ? route('projects.show', $project) : route('purchase-requests.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/external-purchases/purchase-requests/create.blade.php ENDPATH**/ ?>