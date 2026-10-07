<?php $__env->startSection('title', __('maintenance.device_passwords_list')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.device_passwords_list')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.device_passwords_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.device_passwords_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('maintenance-device-passwords.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('maintenance.add_device_password')); ?>

    </a>
</div>

<div class="card overflow-hidden">
    <?php if($devicePasswords->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-key text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('maintenance.no_device_passwords')); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.device_name')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.password')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.notes')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $devicePasswords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $devicePassword): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group" x-data="{ show: false }">
                        <td class="px-5 py-4 font-bold text-slate-800"><?php echo e($devicePassword->device_name); ?></td>
                        <td class="px-5 py-4" dir="ltr">
                            <span class="font-mono text-sm text-slate-600" x-show="show"><?php echo e($devicePassword->password); ?></span>
                            <span class="font-mono text-sm text-slate-400" x-show="!show">••••••••</span>
                            <button type="button" @click="show = !show" class="text-slate-400 hover:text-slate-700 ms-2">
                                <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-xs"></i>
                            </button>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500"><?php echo e($devicePassword->notes ?: '—'); ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('maintenance-device-passwords.edit', $devicePassword)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route('maintenance-device-passwords.destroy', $devicePassword)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/device-passwords/index.blade.php ENDPATH**/ ?>