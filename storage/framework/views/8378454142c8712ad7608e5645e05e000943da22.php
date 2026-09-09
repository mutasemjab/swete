<?php $__env->startSection('title', $purchaseRequest->number); ?>
<?php $__env->startSection('breadcrumb', $purchaseRequest->number); ?>

<?php
    $myApproval = $purchaseRequest->approvals->firstWhere('user_id', auth()->id());
    $canDecide  = $myApproval?->decision === 'pending';
?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($purchaseRequest->number); ?>

            <span class="badge bg-<?php echo e($purchaseRequest->status_color); ?>-100 text-<?php echo e($purchaseRequest->status_color); ?>-700"><?php echo e(__('external_purchases.status_' . $purchaseRequest->status)); ?></span>
        </h1>
        <p class="page-subtitle"><?php echo e(__('external_purchases.purchase_request')); ?></p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo e(route('purchase-requests.print', $purchaseRequest)); ?>" target="_blank" class="btn-primary">
            <i class="fa-solid fa-print"></i>
            <?php echo e(__('external_purchases.print')); ?>

        </a>
        <?php if($purchaseRequest->isEditable()): ?>
        <a href="<?php echo e(route('purchase-requests.edit', $purchaseRequest)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
        <?php endif; ?>
        <a href="<?php echo e(route('purchase-requests.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('external_purchases.approvals')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <div class="flex flex-wrap gap-3 mb-4">
            <?php $__empty_1 = true; $__currentLoopData = $purchaseRequest->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2">
                    <span class="w-2 h-2 rounded-full
                        <?php if($approval->decision === 'approved'): ?> bg-emerald-500
                        <?php elseif($approval->decision === 'rejected'): ?> bg-rose-500
                        <?php else: ?> bg-amber-400 <?php endif; ?>"></span>
                    <span class="text-sm font-semibold text-slate-700"><?php echo e($approval->user?->name); ?></span>
                    <span class="text-xs text-slate-400">— <?php echo e(__('external_purchases.decision_' . $approval->decision)); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-400"><?php echo e(__('external_purchases.no_approvers_configured')); ?></p>
            <?php endif; ?>
        </div>

        <?php if($purchaseRequest->canApproveManually()): ?>
        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <form action="<?php echo e(route('purchase-requests.approve-manually', $purchaseRequest)); ?>" method="POST"
                  onsubmit="return confirm('<?php echo e(__('external_purchases.manual_approve_confirm')); ?>')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-check-double"></i>
                    <?php echo e(__('external_purchases.manual_approve')); ?>

                </button>
            </form>
            <p class="text-xs text-slate-400"><?php echo e(__('external_purchases.manual_approve_hint')); ?></p>
        </div>
        <?php endif; ?>

        <?php if($canDecide): ?>
        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <form action="<?php echo e(route('purchase-requests.approve', $purchaseRequest)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-check"></i>
                    <?php echo e(__('external_purchases.approve')); ?>

                </button>
            </form>
            <form action="<?php echo e(route('purchase-requests.reject', $purchaseRequest)); ?>" method="POST"
                  x-data="{ note: '' }">
                <?php echo csrf_field(); ?>
                <div class="flex items-center gap-2">
                    <input type="text" name="note" x-model="note" placeholder="<?php echo e(__('external_purchases.rejection_note_placeholder')); ?>" class="form-input form-input-sm">
                    <button type="submit" class="btn-danger btn-sm">
                        <i class="fa-solid fa-xmark"></i>
                        <?php echo e(__('external_purchases.reject')); ?>

                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($purchaseRequest->status === 'approved'): ?>
<div class="card px-6 py-5 mb-5">
    <p class="text-sm text-slate-600 mb-4"><?php echo e(__('external_purchases.approved_hint')); ?></p>

    <?php if(! $purchaseRequest->supplier?->email): ?>
        <p class="text-sm text-rose-600 bg-rose-50 border border-rose-100 rounded-xl px-4 py-3 mb-4">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo e(__('external_purchases.supplier_email_missing')); ?>

        </p>
    <?php endif; ?>

    <form action="<?php echo e(route('purchase-requests.mark-sent', $purchaseRequest)); ?>" method="POST" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="form-label"><?php echo e(__('external_purchases.email_subject')); ?></label>
            <input type="text" name="email_subject" value="<?php echo e(old('email_subject', $emailTemplate['subject'])); ?>" dir="ltr"
                   class="form-input <?php $__errorArgs = ['email_subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['email_subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="form-label"><?php echo e(__('external_purchases.email_body')); ?></label>
            <textarea name="email_body" rows="6" dir="ltr" class="form-input <?php $__errorArgs = ['email_body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('email_body', $emailTemplate['body'])); ?></textarea>
            <?php $__errorArgs = ['email_body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="btn-primary" <?php if(! $purchaseRequest->supplier?->email): echo 'disabled'; endif; ?>>
            <i class="fa-solid fa-paper-plane"></i>
            <?php echo e(__('external_purchases.mark_sent')); ?>

        </button>
    </form>
</div>
<?php endif; ?>

<?php if(in_array($purchaseRequest->status, ['sent', 'manufacturing', 'awaiting_price_quotes'])): ?>
<div class="card px-6 py-5 mb-5">
    <h3 class="font-bold text-slate-700 mb-4"><?php echo e(__('external_purchases.manufacturing_info')); ?></h3>
    <form action="<?php echo e(route('purchase-requests.manufacturing', $purchaseRequest)); ?>" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <?php echo csrf_field(); ?>
        <div>
            <label class="form-label"><?php echo e(__('external_purchases.so_number')); ?></label>
            <input type="text" name="so_number" value="<?php echo e(old('so_number', $purchaseRequest->so_number)); ?>" dir="ltr" class="form-input <?php $__errorArgs = ['so_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['so_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="form-label"><?php echo e(__('external_purchases.ready_date')); ?></label>
            <input type="date" name="ready_date" value="<?php echo e(old('ready_date', $purchaseRequest->ready_date?->toDateString())); ?>" class="form-input <?php $__errorArgs = ['ready_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['ready_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="btn-secondary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('app.save')); ?>

        </button>
    </form>
</div>
<?php endif; ?>

<?php if(in_array($purchaseRequest->status, ['manufacturing', 'awaiting_price_quotes'])): ?>
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('external_purchases.attachments')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $purchaseRequest->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="<?php echo e($attachment->url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-link"></i>
                    <?php echo e($attachment->label ?: $attachment->url); ?>

                </a>
                <form action="<?php echo e(route('purchase-requests.attachments.destroy', [$purchaseRequest, $attachment])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('external_purchases.no_attachments')); ?></p>
        <?php endif; ?>

        <form action="<?php echo e(route('purchase-requests.attachments.store', $purchaseRequest)); ?>" method="POST" class="flex items-end gap-3 flex-wrap mt-4 pt-4 border-t border-slate-100">
            <?php echo csrf_field(); ?>
            <div class="flex-1 min-w-56">
                <label class="form-label"><?php echo e(__('external_purchases.attachment_url')); ?></label>
                <input type="text" name="url" placeholder="https://..." dir="ltr" class="form-input">
            </div>
            <div class="flex-1 min-w-40">
                <label class="form-label"><?php echo e(__('external_purchases.attachment_label')); ?></label>
                <input type="text" name="label" class="form-input">
            </div>
            <button type="submit" class="btn-secondary">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('external_purchases.attachment_add')); ?>

            </button>
        </form>
    </div>
</div>

<div class="card px-6 py-5 mb-5 flex items-center justify-between">
    <p class="text-sm text-slate-600"><?php echo e(__('external_purchases.ship_hint')); ?></p>
    <a href="<?php echo e(route('purchase-requests.ship', ['purchase_request_ids' => [$purchaseRequest->id]])); ?>" class="btn-primary">
        <i class="fa-solid fa-ship"></i>
        <?php echo e(__('external_purchases.send_to_shipping_companies')); ?>

    </a>
</div>
<?php endif; ?>

<?php if($purchaseRequest->shippingRequests->isNotEmpty()): ?>
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('external_purchases.shipping_requests_sent')); ?></h3>
    </div>
    <div class="px-6 py-5 flex flex-wrap gap-2">
        <?php $__currentLoopData = $purchaseRequest->shippingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge bg-violet-100 text-violet-700"><?php echo e($sr->shippingCompany?->localized_name); ?> — <?php echo e($sr->sent_at->format('Y-m-d')); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

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
            <dd class="font-bold text-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $purchaseRequest->request_address_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <p><?php echo e($line); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    —
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_shipping_address')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php $__empty_1 = true; $__currentLoopData = $purchaseRequest->shipping_address_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <p><?php echo e($line); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    —
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('external_purchases.request_location_scope')); ?></dt>
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

<?php if($purchaseRequest->additionalNotes->isNotEmpty()): ?>
<div class="card px-6 py-5 mb-5">
    <h3 class="font-bold text-slate-700 mb-3"><?php echo e(__('external_purchases.additional_notes')); ?></h3>
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <?php $__currentLoopData = $purchaseRequest->additionalNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e($note->label); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($note->value ?: '—'); ?></dd>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </dl>
</div>
<?php endif; ?>

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
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.item_ercd')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('accounting.invoice_item_total')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('external_purchases.item_features')); ?></th>
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
                    <td class="px-5 py-3 text-slate-700"><?php echo e($item->ercd ?? '—'); ?></td>
                    <td class="px-5 py-3 text-slate-700"><?php echo e(number_format($item->unit_price, 3)); ?></td>
                    <td class="px-5 py-3 font-bold text-slate-800"><?php echo e(number_format($item->total, 3)); ?></td>
                    <td class="px-5 py-3 text-slate-600 text-xs">
                        <?php $__empty_1 = true; $__currentLoopData = $item->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="badge bg-slate-100 text-slate-600 me-1 mb-1"><?php echo e($feature->value); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            —
                        <?php endif; ?>
                    </td>
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