<?php $routePrefix = $type === 'service_call' ? 'service-calls' : 'tenders'; ?>


<?php $__env->startSection('title', __('tenders.list_' . $type)); ?>
<?php $__env->startSection('breadcrumb', __('tenders.type_' . $type . '_plural')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('tenders.list_' . $type)); ?></h1>
        <p class="page-subtitle"><?php echo e(__('tenders.tenders_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route("{$routePrefix}.create")); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('tenders.add_' . $type)); ?>

    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-gavel text-orange-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($tenders->total()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('tenders.total_tenders')); ?></p>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route("{$routePrefix}.index")); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                   placeholder="<?php echo e(__('app.search')); ?>..."
                   class="form-input ps-10">
        </div>
    </div>
    <select name="status" class="form-select w-44">
        <option value=""><?php echo e(__('tenders.all_statuses_tender')); ?></option>
        <?php $__currentLoopData = ['open','closed','won','lost']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(__('tenders.tender_status_' . $status)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny(['search','status'])): ?>
        <a href="<?php echo e(route("{$routePrefix}.index")); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden">
    <?php if($tenders->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-gavel text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                <?php echo e(request()->hasAny(['search','status']) ? __('tenders.no_tenders_search') : __('tenders.no_tenders')); ?>

            </p>
            <?php if(!request()->hasAny(['search','status'])): ?>
                <a href="<?php echo e(route("{$routePrefix}.create")); ?>" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    <?php echo e(__('tenders.add_first_tender')); ?>

                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.tender_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.tender_title')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('tenders.tender_entity_name')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell"><?php echo e(__('tenders.tender_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.tender_submission_deadline')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.tender_status')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                        $statusStyles = [
                            'open'   => 'bg-blue-100 text-blue-700',
                            'closed' => 'bg-slate-100 text-slate-500',
                            'won'    => 'bg-emerald-100 text-emerald-700',
                            'lost'   => 'bg-rose-100 text-rose-700',
                        ];
                    ?>
                    <?php $__currentLoopData = $tenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg text-sm"><?php echo e($tender->number); ?></span>
                        </td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-800"><?php echo e($tender->localized_title); ?></p></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600"><?php echo e($tender->localized_entity_name); ?></td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600"><?php echo e($tender->party?->localized_name ?? '—'); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($tender->submission_deadline->format('Y-m-d')); ?></td>
                        <td class="px-5 py-4">
                            <span class="badge <?php echo e($statusStyles[$tender->status]); ?>"><?php echo e(__('tenders.tender_status_' . $tender->status)); ?></span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?php echo e(route("{$routePrefix}.edit", $tender)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route("{$routePrefix}.destroy", $tender)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
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

        <?php if($tenders->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($tenders->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($tenders->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($tenders->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($tenders->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($tenders->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($tenders->hasMorePages()): ?>
                    <a href="<?php echo e($tenders->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/index.blade.php ENDPATH**/ ?>