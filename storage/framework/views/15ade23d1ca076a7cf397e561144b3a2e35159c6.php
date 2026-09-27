<?php $discount = $discount ?? null; ?>
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-percent text-orange-500 text-sm"></i>
            <?php echo e(__('tenders.ciat_discounts_list')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label"><?php echo e(__('tenders.ciat_type')); ?> <span class="text-rose-500">*</span></label>
            <select name="material_id" class="js-select2 form-select <?php $__errorArgs = ['material_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($material->id); ?>" <?php if(old('material_id', $discount?->material_id) == $material->id): echo 'selected'; endif; ?>><?php echo e($material->localized_name); ?> (<?php echo e($material->code); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['material_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="form-label"><?php echo e(__('tenders.ciat_discount_percent')); ?> <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" max="100" name="discount_percent" value="<?php echo e(old('discount_percent', $discount?->discount_percent)); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['discount_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php if($discount): ?>
        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer" <?php if(old('status', $discount->status ?? true)): echo 'checked'; endif; ?>>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('app.active')); ?></span>
            </label>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/ciat-discounts/_form.blade.php ENDPATH**/ ?>