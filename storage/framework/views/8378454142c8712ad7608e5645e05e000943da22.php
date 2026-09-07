<?php $__env->startSection('title', $purchaseRequest->number); ?>
<?php $__env->startSection('breadcrumb', $purchaseRequest->number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e($purchaseRequest->number); ?></h1>
        <p class="page-subtitle"><?php echo e(__('external_purchases.purchase_request')); ?></p>
    </div>
    <a href="<?php echo e(route('purchase-requests.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_date')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($purchaseRequest->date->format('Y-m-d')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_supplier')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($purchaseRequest->supplier?->localized_name); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_linked_to')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($purchaseRequest->project): ?>
                    <?php echo e(__('external_purchases.link_type_project')); ?>:
                    <a href="<?php echo e(route('projects.show', $purchaseRequest->project)); ?>" class="text-indigo-600 hover:underline"><?php echo e($purchaseRequest->project->number); ?></a>
                <?php elseif($purchaseRequest->serviceCall): ?>
                    <?php echo e(__('external_purchases.link_type_service_call')); ?>: <?php echo e($purchaseRequest->serviceCall->number); ?>

                <?php else: ?>
                    <?php echo e(__('external_purchases.link_type_stock')); ?>

                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_branch')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($purchaseRequest->branch?->localized_name); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_address')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($purchaseRequest->localized_request_address ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_shipping_address')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($purchaseRequest->shipping_address ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_location_scope')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($purchaseRequest->location_scope === 'outside_jordan'): ?>
                    <?php echo e(__('tenders.location_outside_jordan')); ?> — <?php echo e($purchaseRequest->country?->localized_name); ?>

                <?php elseif($purchaseRequest->location_scope === 'inside_jordan'): ?>
                    <?php echo e(__('tenders.location_inside_jordan')); ?> — <?php echo e($purchaseRequest->localized_governorate); ?>

                <?php else: ?>
                    —
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_currency')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($purchaseRequest->currency?->localized_name ?? '—'); ?></dd>
        </div>
        <?php if($purchaseRequest->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($purchaseRequest->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('external_purchases.request_items')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.material')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_total')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $purchaseRequest->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-5 py-3">
                        <span class="font-bold text-slate-800"><?php echo e($item->material?->localized_name); ?></span>
                        <span class="text-xs text-slate-400 ms-1"><?php echo e($item->material?->unit?->symbol); ?></span>
                    </td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->quantity, 3)); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->unit_price, 3)); ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?php echo e(number_format($item->total, 3)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex justify-end">
    <div class="card px-6 py-5 w-full sm:w-80">
        <div class="flex justify-between pt-2">
            <dt class="text-slate-700 font-bold"><?php echo e(__('external_purchases.request_total')); ?></dt>
            <dd class="font-black text-lg text-cyan-700"><?php echo e(number_format($purchaseRequest->total, 3)); ?></dd>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/external-purchases/purchase-requests/show.blade.php ENDPATH**/ ?>