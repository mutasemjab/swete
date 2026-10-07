<?php $__env->startSection('title', __('customer_portal.login_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-center" style="min-height: 80vh;">
    <div class="cp-card w-full max-w-sm">
        <div class="text-center mb-5">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-screwdriver-wrench text-white text-xl"></i>
            </div>
            <p class="font-black text-lg text-slate-800"><?php echo e(config('app.name', 'ERP')); ?></p>
            <p class="text-sm text-slate-500"><?php echo e(__('customer_portal.login_subtitle')); ?></p>
        </div>

        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-rose-600 text-sm font-bold text-center mb-4"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <form method="POST" action="<?php echo e(route('customer-portal.authenticate')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="cp-label"><?php echo e(__('customer_portal.customer_code')); ?></label>
                <input type="text" name="code" value="<?php echo e(old('code')); ?>" dir="ltr" class="cp-input" required autofocus>
            </div>
            <div>
                <label class="cp-label"><?php echo e(__('app.password')); ?></label>
                <input type="password" name="password" class="cp-input" required>
            </div>
            <button type="submit" class="cp-btn-primary w-full justify-center">
                <i class="fa-solid fa-right-to-bracket"></i>
                <?php echo e(__('app.login_btn')); ?>

            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer-portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/customer-portal/auth/login.blade.php ENDPATH**/ ?>