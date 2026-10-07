<?php $__env->startSection('title', __('settings.edit_user')); ?>
<?php $__env->startSection('breadcrumb', __('settings.edit_user')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.edit_user')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.edit_user_subtitle')); ?></p>
    </div>
    <div class="flex gap-2">
        <a href="<?php echo e(route('settings.users.show', $user)); ?>" class="btn-secondary">
            <i class="fa-solid fa-eye"></i>
            <?php echo e(__('app.view')); ?>

        </a>
        <a href="<?php echo e(route('settings.users.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<form action="<?php echo e(route('settings.users.update', $user)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

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
                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                       class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" dir="ltr"
                       class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
                <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" dir="ltr"
                       class="form-input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['phone'];
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

    <div class="card mb-5" x-data="{ changePass: false }">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-lock text-indigo-500 text-sm"></i>
                <?php echo e(__('settings.security')); ?>

            </h3>
            <button type="button" @click="changePass = !changePass"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                <span x-text="changePass ? '<?php echo e(__('app.cancel_change')); ?>' : '<?php echo e(__('app.change_password')); ?>'"></span>
            </button>
        </div>
        <div x-show="changePass" x-transition class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('app.password')); ?></label>
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
                <label class="form-label"><?php echo e(__('app.password_confirm')); ?></label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>
        <div x-show="!changePass" class="px-6 py-4">
            <p class="text-sm text-slate-400"><?php echo e(__('app.password_min')); ?></p>
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
                        <option value="<?php echo e($role->name); ?>"
                                <?php if(old('role', $user->roles->first()?->name) === $role->name): echo 'selected'; endif; ?>>
                            <?php echo e($role->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-7">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                           <?php if(old('status', $user->status)): echo 'checked'; endif; ?>>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('app.active')); ?></span>
                </label>
            </div>

            <div class="sm:col-span-2">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="is_maintenance_manager" value="0">
                    <input type="checkbox" name="is_maintenance_manager" value="1" class="sr-only peer"
                           <?php if(old('is_maintenance_manager', $user->is_maintenance_manager)): echo 'checked'; endif; ?>>
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

    <?php if($user->id !== auth()->id()): ?>
    <div class="card mb-5 border-rose-200">
        <div class="card-header border-rose-100">
            <h3 class="font-bold text-rose-700 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                <?php echo e(__('app.danger_zone')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-slate-700"><?php echo e(__('settings.delete_user')); ?></p>
                <p class="text-xs text-slate-400 mt-0.5"><?php echo e(__('app.irreversible')); ?></p>
            </div>
            <button type="button"
                    @click="$dispatch('delete-confirm', {
                        action: '<?php echo e(route('settings.users.destroy', $user)); ?>',
                        message: '<?php echo e(__('settings.delete_user_confirm')); ?> <?php echo e($user->name); ?>؟'
                    })"
                    class="btn-danger btn-sm flex-shrink-0">
                <i class="fa-solid fa-trash"></i>
                <?php echo e(__('app.delete')); ?>

            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('app.save')); ?>

        </button>
        <a href="<?php echo e(route('settings.users.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/users/edit.blade.php ENDPATH**/ ?>