<?php $__env->startSection('title', __('external_purchases.requests_list')); ?>
<?php $__env->startSection('breadcrumb', __('external_purchases.purchase_requests')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('external_purchases.requests_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('external_purchases.requests_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('purchase-requests.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('external_purchases.add_purchase_request')); ?>

    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-truck-ramp-box text-cyan-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($purchaseRequests->total()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('external_purchases.total_requests')); ?></p>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route('purchase-requests.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <div class="flex-1 min-w-48">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                   placeholder="<?php echo e(__('app.search')); ?>..."
                   class="form-input ps-10">
        </div>
    </div>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny(['search'])): ?>
        <a href="<?php echo e(route('purchase-requests.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden">
    <?php if($purchaseRequests->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-truck-ramp-box text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                <?php echo e(request()->hasAny(['search']) ? __('external_purchases.no_requests_search') : __('external_purchases.no_requests')); ?>

            </p>
            <?php if(!request()->hasAny(['search'])): ?>
                <a href="<?php echo e(route('purchase-requests.create')); ?>" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    <?php echo e(__('external_purchases.add_first_request')); ?>

                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.request_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.request_date')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.request_supplier')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('external_purchases.request_linked_to')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.request_total')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $purchaseRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('purchase-requests.show', $pr)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors"><?php echo e($pr->number); ?></a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($pr->date->format('Y-m-d')); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($pr->supplier?->localized_name); ?></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                            <?php if($pr->project): ?>
                                <?php echo e(__('external_purchases.link_type_project')); ?>: <?php echo e($pr->project->number); ?>

                            <?php elseif($pr->serviceCall): ?>
                                <?php echo e(__('external_purchases.link_type_service_call')); ?>: <?php echo e($pr->serviceCall->number); ?>

                            <?php else: ?>
                                <?php echo e(__('external_purchases.link_type_stock')); ?>

                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800"><?php echo e(number_format($pr->total, 3)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($purchaseRequests->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($purchaseRequests->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($purchaseRequests->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($purchaseRequests->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($purchaseRequests->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($purchaseRequests->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($purchaseRequests->hasMorePages()): ?>
                    <a href="<?php echo e($purchaseRequests->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/external-purchases/purchase-requests/index.blade.php ENDPATH**/ ?>