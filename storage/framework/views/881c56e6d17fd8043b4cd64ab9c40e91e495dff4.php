<?php $__env->startSection('title', __('maintenance.contract_payments')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.contract_payments')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.contract_payments')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.contracts_subtitle')); ?></p>
    </div>
    <?php if($dueCount > 0): ?>
        <a href="<?php echo e(route('contract-payments.index', ['due' => 1])); ?>" class="badge bg-rose-100 text-rose-700 !text-sm !px-4 !py-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php echo e(__('maintenance.payments_due_count', ['count' => $dueCount])); ?>

        </a>
    <?php endif; ?>
</div>

<form method="GET" action="<?php echo e(route('contract-payments.index')); ?>" class="card px-5 py-4 mb-4 flex flex-wrap gap-3">
    <select name="assigned_to" class="js-select2 form-select w-56">
        <option value=""><?php echo e(__('maintenance.all_employees')); ?></option>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($employee->id); ?>" <?php if(request('assigned_to') == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <label class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-xl cursor-pointer">
        <input type="checkbox" name="due" value="1" <?php if(request('due')): echo 'checked'; endif; ?>>
        <span class="text-sm text-slate-600"><?php echo e(__('maintenance.payments_due_only')); ?></span>
    </label>
    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-filter"></i>
        <?php echo e(__('app.search')); ?>

    </button>
    <?php if(request()->hasAny(['assigned_to', 'due'])): ?>
        <a href="<?php echo e(route('contract-payments.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <?php echo e(__('app.clear_filters')); ?>

        </a>
    <?php endif; ?>
</form>

<div class="card overflow-hidden" x-data="{
        selected: [],
        showAssignModal: false,
        toggleAll(checked) { this.selected = checked ? <?php echo e($payments->getCollection()->whereNull('invoice_id')->pluck('id')->values()->toJson()); ?> : []; },
      }">
    <?php if($payments->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-money-check-dollar text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg"><?php echo e(__('maintenance.no_payments')); ?></p>
        </div>
    <?php else: ?>
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between" x-show="selected.length > 0" x-cloak>
            <p class="text-sm font-semibold text-slate-600" x-text="selected.length + ' <?php echo e(__('app.selected')); ?>'"></p>
            <button type="button" @click="showAssignModal = true" class="btn-secondary btn-sm">
                <i class="fa-solid fa-paper-plane"></i>
                <?php echo e(__('maintenance.payment_send_to_employee')); ?>

            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 w-10">
                            <input type="checkbox" @change="toggleAll($event.target.checked)" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.contract_number')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.contract_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.payment_due_date')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.payment_amount')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('maintenance.payment_assigned_to')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50 transition-colors <?php if($payment->isOverdue()): ?> bg-rose-50/60 <?php elseif($payment->isDueToday()): ?> bg-amber-50/60 <?php endif; ?>">
                        <td class="px-5 py-4">
                            <?php if(! $payment->invoice_id): ?>
                                <input type="checkbox" value="<?php echo e($payment->id); ?>" x-model.number="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <a href="<?php echo e(route('maintenance-contracts.show', $payment->contract)); ?>" class="font-mono font-bold text-slate-700 hover:text-indigo-600 transition-colors"><?php echo e($payment->contract?->number); ?></a>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600"><?php echo e($payment->contract?->customer?->localized_name); ?></td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            <span dir="ltr"><?php echo e($payment->due_date->format('Y-m-d')); ?></span>
                            <?php if($payment->isOverdue()): ?>
                                <span class="badge bg-rose-100 text-rose-700 ms-1.5"><?php echo e(__('maintenance.payment_overdue')); ?></span>
                            <?php elseif($payment->isDueToday()): ?>
                                <span class="badge bg-amber-100 text-amber-700 ms-1.5"><?php echo e(__('maintenance.payment_due_today')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800" dir="ltr"><?php echo e(number_format($payment->amount, 3)); ?> <?php echo e($payment->currency?->code); ?></td>
                        <td class="px-5 py-4">
                            <?php if($payment->invoice_id): ?>
                                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('maintenance.payment_status_invoiced')); ?></span>
                            <?php elseif($payment->assignee): ?>
                                <span class="badge bg-violet-100 text-violet-700"><?php echo e($payment->assignee->name); ?></span>
                            <?php else: ?>
                                <span class="text-xs text-slate-400"><?php echo e(__('maintenance.payment_unassigned')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <?php if($payment->invoice_id): ?>
                                    <a href="<?php echo e(route('accounting.invoices.show', $payment->invoice_id)); ?>"
                                       class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="<?php echo e(__('maintenance.payment_view_invoice')); ?>">
                                        <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                                    </a>
                                <?php elseif($payment->assigned_to === Auth::id()): ?>
                                    <form action="<?php echo e(route('contract-payments.convert-to-invoice', $payment)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-secondary btn-sm" title="<?php echo e(__('maintenance.payment_convert_to_invoice')); ?>">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                            <?php echo e(__('maintenance.payment_convert_to_invoice')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($payments->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($payments->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($payments->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($payments->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($payments->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($payments->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($payments->hasMorePages()): ?>
                    <a href="<?php echo e($payments->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        
        <div x-show="showAssignModal" x-cloak
             class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
            <div @click.outside="showAssignModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <form action="<?php echo e(route('contract-payments.assign')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="payment_ids[]" :value="id">
                    </template>
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800"><?php echo e(__('maintenance.payment_send_to_employee')); ?></h3>
                        <p class="text-xs text-slate-400 mt-1"><?php echo e(__('maintenance.payment_send_to_employee_hint')); ?></p>
                    </div>
                    <div class="px-6 py-5">
                        <label class="form-label"><?php echo e(__('maintenance.payment_select_employee')); ?> <span class="text-rose-500">*</span></label>
                        <select name="assigned_to" class="form-select" required>
                            <option value=""><?php echo e(__('app.select')); ?></option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showAssignModal = false" class="btn-secondary"><?php echo e(__('app.cancel')); ?></button>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            <?php echo e(__('maintenance.payment_send')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/contracts/payments-index.blade.php ENDPATH**/ ?>