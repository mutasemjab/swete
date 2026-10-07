<?php $__env->startSection('title', __('customer_portal.new_request')); ?>

<?php $__env->startSection('bar'); ?>
<div class="cp-bar">
    <a href="<?php echo e(route('customer-portal.dashboard')); ?>" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    </a>
    <div class="flex-1">
        <p class="font-black leading-tight"><?php echo e(__('customer_portal.new_request')); ?></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-card">
    <form method="POST" action="<?php echo e(route('customer-portal.maintenance-requests.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="cp-label"><?php echo e(__('maintenance.request_description')); ?> <span class="text-rose-500">*</span></label>
            <textarea name="description" rows="4" class="cp-input <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm font-bold mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="cp-label"><?php echo e(__('maintenance.request_preferred_date')); ?></label>
            <input type="date" name="preferred_date" value="<?php echo e(old('preferred_date')); ?>" dir="ltr" class="cp-input">
        </div>
        <button type="submit" class="cp-btn-primary w-full justify-center">
            <i class="fa-solid fa-paper-plane"></i>
            <?php echo e(__('customer_portal.submit_request')); ?>

        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer-portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/customer-portal/maintenance-requests/create.blade.php ENDPATH**/ ?>