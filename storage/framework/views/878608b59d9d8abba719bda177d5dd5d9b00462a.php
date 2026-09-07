<?php $__env->startSection('title', __('settings.branches_list')); ?>
<?php $__env->startSection('breadcrumb', __('settings.branches')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('settings.branches_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('settings.branches_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('settings.branches.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('settings.add_branch')); ?>

    </a>
</div>


<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-code-branch text-indigo-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($branches->count()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('settings.total_branches')); ?></p>
        </div>
    </div>
    <?php $main = $branches->firstWhere('is_main', true); ?>
    <?php if($main): ?>
    <div class="card px-5 py-4 flex items-center gap-4 border-amber-200">
        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-star text-amber-600"></i>
        </div>
        <div>
            <p class="text-sm font-black text-slate-800"><?php echo e($main->localized_name); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('settings.main_branch')); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="card overflow-hidden">
    <?php if($branches->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-code-branch text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('settings.no_branches')); ?></p>
            <a href="<?php echo e(route('settings.branches.create')); ?>" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('settings.add_first_branch')); ?>

            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('settings.branch_name')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('app.phone')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell"><?php echo e(__('settings.branch_address')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.status')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl <?php echo e($branch->is_main ? 'bg-amber-100' : 'bg-slate-100'); ?> flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-<?php echo e($branch->is_main ? 'star' : 'code-branch'); ?> text-<?php echo e($branch->is_main ? 'amber' : 'slate'); ?>-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800"><?php echo e($branch->localized_name); ?></p>
                                    <?php if($branch->is_main): ?>
                                        <span class="badge bg-amber-100 text-amber-700 text-[10px]"><?php echo e(__('settings.main_branch')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="text-sm text-slate-600" dir="ltr"><?php echo e($branch->phone ?? '—'); ?></span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <span class="text-sm text-slate-600 line-clamp-1"><?php echo e(empty($branch->localized_address_lines) ? '—' : implode(' — ', $branch->localized_address_lines)); ?></span>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($branch->status): ?>
                                <span class="badge bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    <?php echo e(__('app.active')); ?>

                                </span>
                            <?php else: ?>
                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    <?php echo e(__('app.inactive')); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('settings.branches.edit', $branch)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', {
                                            action: '<?php echo e(route('settings.branches.destroy', $branch)); ?>',
                                            message: '<?php echo e(__('app.delete_confirm_msg')); ?>'
                                        })"
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/settings/branches/index.blade.php ENDPATH**/ ?>