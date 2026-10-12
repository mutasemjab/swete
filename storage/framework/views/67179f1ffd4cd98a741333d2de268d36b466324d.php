<?php $__env->startSection('title', $agreement->number); ?>
<?php $__env->startSection('breadcrumb', $agreement->number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e($agreement->number); ?></h1>
        <p class="page-subtitle"><?php echo e($agreement->party?->localized_name); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo e($agreement->url); ?>" target="_blank" rel="noopener" class="btn-secondary">
            <i class="fa-solid fa-file-pdf"></i>
            <?php echo e(__('agreements.view_current_file')); ?>

        </a>
        <a href="<?php echo e(route('agreements.edit', $agreement)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <a href="<?php echo e(route('agreements.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('agreements.party_type')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($agreement->party_type === 'customer'): ?>
                    <span class="badge bg-violet-100 text-violet-700"><?php echo e(__('agreements.party_type_customer')); ?></span>
                <?php else: ?>
                    <span class="badge bg-orange-100 text-orange-700"><?php echo e(__('agreements.party_type_supplier')); ?></span>
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('agreements.party')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($agreement->party?->localized_name); ?> (<?php echo e($agreement->party?->code); ?>)</dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('app.created_at')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($agreement->created_at->format('Y-m-d H:i')); ?> — <?php echo e($agreement->creator?->name); ?></dd>
        </div>
        <?php if($agreement->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('app.notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($agreement->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/agreements/show.blade.php ENDPATH**/ ?>