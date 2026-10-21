<?php $__env->startSection('title', __('tenders.projects_under_review_list')); ?>
<?php $__env->startSection('breadcrumb', __('tenders.projects_under_review')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('tenders.projects_under_review_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('tenders.projects_under_review_subtitle')); ?></p>
    </div>
</div>

<form method="GET" action="<?php echo e(route('project-reviews.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
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
        <a href="<?php echo e(route('project-reviews.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden">
    <?php if($projects->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-hourglass-half text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('tenders.no_projects_under_review')); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.project_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.project_title')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden lg:table-cell"><?php echo e(__('tenders.project_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('tenders.project_tender')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('projects.show', $project)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors bg-slate-100 px-2 py-1 rounded-lg text-sm"><?php echo e($project->number); ?></a>
                            <?php if($project->variation_round > 0): ?>
                                <span class="badge bg-purple-100 text-purple-700">P<?php echo e($project->variation_round); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800"><?php echo e($project->localized_title); ?></p>
                            <?php if($project->review_reason): ?>
                                <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo e($project->review_reason); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-sm text-slate-600"><?php echo e($project->customer?->localized_name ?? '—'); ?></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                            <?php if($project->tender): ?>
                                <a href="<?php echo e(route('tenders.show', $project->tender)); ?>" class="text-indigo-600 hover:underline"><?php echo e($project->tender->number); ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?php echo e(route('projects.show', $project)); ?>" class="btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i> <?php echo e(__('app.view')); ?>

                                </a>
                                <form action="<?php echo e(route('project-reviews.approve', $project)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn-primary btn-sm">
                                        <i class="fa-solid fa-check"></i> <?php echo e(__('tenders.project_approve')); ?>

                                    </button>
                                </form>
                                <form action="<?php echo e(route('project-reviews.reject', $project)); ?>" method="POST"
                                      @submit="if (! confirm('<?php echo e(__('tenders.project_reject_confirm')); ?>')) $event.preventDefault()">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn-danger btn-sm">
                                        <i class="fa-solid fa-xmark"></i> <?php echo e(__('tenders.project_reject')); ?>

                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($projects->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($projects->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($projects->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($projects->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($projects->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($projects->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($projects->hasMorePages()): ?>
                    <a href="<?php echo e($projects->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if($variationOrders->isNotEmpty()): ?>
<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-circle-plus text-purple-500 text-sm"></i>
            <?php echo e(__('tenders.variation_orders_section')); ?>

        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.project_number')); ?></th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_number')); ?></th>
                    <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('tenders.project_tender')); ?></th>
                    <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $variationOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-slate-50/50 transition-colors bg-purple-50/30">
                    <td class="px-5 py-4">
                        <a href="<?php echo e(route('projects.show', $vo->project)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors bg-slate-100 px-2 py-1 rounded-lg text-sm"><?php echo e($vo->project->number); ?></a>
                        <span class="badge bg-purple-100 text-purple-700"><?php echo e(__('tenders.variation_order_badge')); ?></span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="<?php echo e(route('price-quotes.show', $vo->priceQuote)); ?>" class="font-mono font-bold text-indigo-600 hover:underline"><?php echo e($vo->priceQuote->number); ?></a>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600">
                        <?php if($vo->priceQuote->tender): ?>
                            <a href="<?php echo e(route('tenders.show', $vo->priceQuote->tender)); ?>" class="text-indigo-600 hover:underline"><?php echo e($vo->priceQuote->tender->number); ?></a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <form action="<?php echo e(route('project-reviews.variation-orders.approve', $vo)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-primary btn-sm">
                                    <i class="fa-solid fa-check"></i> <?php echo e(__('tenders.project_approve')); ?>

                                </button>
                            </form>
                            <form action="<?php echo e(route('project-reviews.variation-orders.reject', $vo)); ?>" method="POST"
                                  @submit="if (! confirm('<?php echo e(__('tenders.project_reject_confirm')); ?>')) $event.preventDefault()">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-danger btn-sm">
                                    <i class="fa-solid fa-xmark"></i> <?php echo e(__('tenders.project_reject')); ?>

                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/project-reviews/index.blade.php ENDPATH**/ ?>