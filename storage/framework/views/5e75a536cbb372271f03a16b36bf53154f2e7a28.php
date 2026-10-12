<?php $__env->startSection('title', __('agreements.agreements_list')); ?>
<?php $__env->startSection('breadcrumb', __('agreements.agreements_list')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('agreements.agreements_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('agreements.agreements_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('agreements.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('agreements.add_agreement')); ?>

    </a>
</div>

<form method="GET" action="<?php echo e(route('agreements.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="party_type" class="form-select w-56">
        <option value=""><?php echo e(__('app.all')); ?></option>
        <option value="customer" <?php if(request('party_type') === 'customer'): echo 'selected'; endif; ?>><?php echo e(__('agreements.party_type_customer')); ?></option>
        <option value="supplier" <?php if(request('party_type') === 'supplier'): echo 'selected'; endif; ?>><?php echo e(__('agreements.party_type_supplier')); ?></option>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny(['party_type'])): ?>
        <a href="<?php echo e(route('agreements.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden">
    <?php if($agreements->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-file-signature text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('agreements.no_agreements')); ?></p>
            <a href="<?php echo e(route('agreements.create')); ?>" class="btn-primary mt-5">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('agreements.add_agreement')); ?>

            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('agreements.agreement_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('agreements.party_type')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('agreements.party')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.created_at')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('agreements.show', $agreement)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors"><?php echo e($agreement->number); ?></a>
                        </td>
                        <td class="px-5 py-4">
                            <?php if($agreement->party_type === 'customer'): ?>
                                <span class="badge bg-violet-100 text-violet-700"><?php echo e(__('agreements.party_type_customer')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-orange-100 text-orange-700"><?php echo e(__('agreements.party_type_supplier')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($agreement->party?->localized_name); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-500"><?php echo e($agreement->created_at->format('Y-m-d')); ?></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route('agreements.show', $agreement)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="<?php echo e(__('app.view')); ?>">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                <a href="<?php echo e(route('agreements.edit', $agreement)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route('agreements.destroy', $agreement)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
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

        <?php if($agreements->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($agreements->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($agreements->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($agreements->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($agreements->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($agreements->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($agreements->hasMorePages()): ?>
                    <a href="<?php echo e($agreements->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/agreements/index.blade.php ENDPATH**/ ?>