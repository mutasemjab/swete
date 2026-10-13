<?php $__env->startSection('title', __('settings.quote_discount_limit')); ?>
<?php $__env->startSection('breadcrumb', __('settings.quote_discount_limit')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.quote_discount_limit')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.quote_discount_limit_subtitle')); ?></p>
    </div>
</div>

<form action="<?php echo e(route('settings.quote-discount-limit.update')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-percent text-amber-500 text-sm"></i>
                <?php echo e(__('settings.quote_discount_limit')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('settings.max_discount_percent')); ?></label>
                <input type="number" name="max_discount_percent" value="<?php echo e(old('max_discount_percent', $limit->max_discount_percent)); ?>"
                       step="0.01" min="0" max="100" dir="ltr" class="form-input <?php $__errorArgs = ['max_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <p class="text-xs text-slate-400 mt-1.5"><?php echo e(__('settings.max_discount_percent_hint')); ?></p>
                <?php $__errorArgs = ['max_discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('settings.max_discount_amount')); ?></label>
                <input type="number" name="max_discount_amount" value="<?php echo e(old('max_discount_amount', $limit->max_discount_amount)); ?>"
                       step="0.001" min="0" dir="ltr" class="form-input <?php $__errorArgs = ['max_discount_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <p class="text-xs text-slate-400 mt-1.5"><?php echo e(__('settings.max_discount_amount_hint')); ?></p>
                <?php $__errorArgs = ['max_discount_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/quote-discount-limit/index.blade.php ENDPATH**/ ?>