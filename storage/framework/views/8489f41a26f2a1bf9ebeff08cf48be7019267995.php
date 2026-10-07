<?php $__env->startSection('title', __('settings.add_user')); ?>
<?php $__env->startSection('breadcrumb', __('settings.add_user')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.add_user')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.add_user_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('settings.users.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route('settings.users.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-user text-indigo-500 text-sm"></i>
                <?php echo e(__('settings.basic_info')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="form-label"><?php echo e(__('app.name')); ?> <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>"
                       class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="<?php echo e(__('app.name')); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('app.email')); ?> <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" dir="ltr"
                       class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="email@example.com">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('app.phone')); ?></label>
                <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" dir="ltr"
                       class="form-input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="+962 7X XXX XXXX">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('app.password')); ?> <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" name="password"
                           class="form-input pe-10 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="<?php echo e(__('app.password_min')); ?>">
                    <button type="button" @click="show = !show"
                            class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                        <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                    </button>
                </div>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('app.password_confirm')); ?> <span class="text-rose-500">*</span></label>
                <input type="password" name="password_confirmation"
                       class="form-input"
                       placeholder="<?php echo e(__('app.password_confirm')); ?>">
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-500 text-sm"></i>
                <?php echo e(__('settings.permissions_section')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="form-label"><?php echo e(__('settings.assign_role')); ?></label>
                <select name="role" class="form-select">
                    <option value=""><?php echo e(__('settings.no_role')); ?></option>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->name); ?>" <?php if(old('role') === $role->name): echo 'selected'; endif; ?>><?php echo e($role->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-slate-400 mt-1.5"><?php echo e(__('settings.login_tip')); ?></p>
            </div>

            <div class="flex items-center gap-4 pt-7">
                <label class="relative inline-flex items-center cursor-pointer" x-data dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           <?php if(old('status', '1') == '1'): echo 'checked'; endif; ?>>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('app.active')); ?></span>
                </label>
            </div>

            <div class="sm:col-span-2">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="is_maintenance_manager" value="0">
                    <input type="checkbox" name="is_maintenance_manager" value="1" class="sr-only peer"
                           <?php if(old('is_maintenance_manager')): echo 'checked'; endif; ?>>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('settings.is_maintenance_manager')); ?></span>
                </label>
                <p class="text-xs text-slate-400 mt-1.5"><?php echo e(__('settings.is_maintenance_manager_hint')); ?></p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('settings.add_user')); ?>

        </button>
        <a href="<?php echo e(route('settings.users.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/users/create.blade.php ENDPATH**/ ?>