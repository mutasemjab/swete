<?php $__env->startSection('title', __('settings.add_role')); ?>
<?php $__env->startSection('breadcrumb', __('settings.add_role')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.add_role')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.add_role_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('settings.roles.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route('settings.roles.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-500 text-sm"></i>
                <?php echo e(__('settings.role_info')); ?>

            </h3>
        </div>
        <div class="px-6 py-5">
            <label class="form-label"><?php echo e(__('settings.role_name')); ?> <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" dir="ltr"
                   class="form-input max-w-xs <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   placeholder="e.g. branch_manager">
            <p class="text-xs text-slate-400 mt-1.5"><?php echo e(__('settings.role_name_hint')); ?></p>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-key text-indigo-500 text-sm"></i>
                <?php echo e(__('settings.role_permissions')); ?>

            </h3>
            <p class="text-xs text-slate-400"><?php echo e(__('settings.select_permissions')); ?></p>
        </div>
        <div class="px-6 py-5 space-y-6">
            <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPerms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div>
                <div class="flex items-center gap-3 mb-3" x-data>
                    <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest"><?php echo e($group); ?></h4>
                    <div class="flex-1 h-px bg-slate-100"></div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-500 hover:text-slate-700">
                        <input type="checkbox"
                               x-ref="groupChk_<?php echo e(Str::slug($group)); ?>"
                               @change="$el.closest('[x-data]').querySelectorAll('input[type=checkbox][name]').forEach(c => c.checked = $el.checked)"
                               class="rounded border-slate-300 text-indigo-600">
                        <?php echo e(__('settings.select_permissions')); ?>

                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <?php $__currentLoopData = $groupPerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-slate-200
                                  hover:border-indigo-300 hover:bg-indigo-50/50 cursor-pointer transition-all
                                  has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50">
                        <input type="checkbox" name="permissions[]" value="<?php echo e($permission->name); ?>"
                               <?php if(in_array($permission->name, old('permissions', []))): echo 'checked'; endif; ?>
                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-mono text-slate-700"><?php echo e($permission->name); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 text-center py-8"><?php echo e(__('settings.no_permissions')); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('settings.add_role')); ?>

        </button>
        <a href="<?php echo e(route('settings.roles.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/roles/create.blade.php ENDPATH**/ ?>