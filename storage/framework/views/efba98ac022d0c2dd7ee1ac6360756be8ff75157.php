<?php $__env->startSection('title', __('settings.purchase_request_reminder_recipients')); ?>
<?php $__env->startSection('breadcrumb', __('settings.purchase_request_reminder_recipients')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.purchase_request_reminder_recipients')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.purchase_request_reminder_recipients_subtitle')); ?></p>
    </div>
</div>

<form action="<?php echo e(route('settings.purchase-request-reminder-recipients.update')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-bell text-indigo-500 text-sm"></i>
                <?php echo e(__('settings.purchase_request_reminder_recipients')); ?>

            </h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-xs text-slate-400 mb-4"><?php echo e(__('settings.purchase_request_reminder_recipients_hint')); ?></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 cursor-pointer hover:border-indigo-300 transition-colors">
                        <input type="checkbox" name="recipient_ids[]" value="<?php echo e($user->id); ?>"
                               <?php if($currentRecipientIds->contains($user->id)): echo 'checked'; endif; ?>>
                        <span>
                            <span class="block font-bold text-slate-800"><?php echo e($user->name); ?></span>
                            <span class="block text-xs text-slate-400" dir="ltr"><?php echo e($user->email); ?></span>
                        </span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('app.save')); ?>

        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/purchase-request-reminder-recipients/index.blade.php ENDPATH**/ ?>