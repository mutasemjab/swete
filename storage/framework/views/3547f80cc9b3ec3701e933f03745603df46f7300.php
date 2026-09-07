<?php $__env->startSection('title', __('settings.countries_list')); ?>
<?php $__env->startSection('breadcrumb', __('settings.countries')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.countries_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.countries_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('settings.countries.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('settings.add_country')); ?>

    </a>
</div>

<div class="card overflow-hidden">
    <?php if($countries->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-earth-americas text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('settings.no_countries')); ?></p>
            <a href="<?php echo e(route('settings.countries.create')); ?>" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('settings.add_first_country')); ?>

            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.country_name')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4"><p class="font-bold text-slate-800"><?php echo e($country->localized_name); ?></p></td>
                        <td class="px-5 py-4">
                            <?php if($country->status): ?>
                                <span class="badge bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span><?php echo e(__('app.active')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-slate-100 text-slate-500"><span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span><?php echo e(__('app.inactive')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('settings.countries.edit', $country)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route('settings.countries.destroy', $country)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/countries/index.blade.php ENDPATH**/ ?>