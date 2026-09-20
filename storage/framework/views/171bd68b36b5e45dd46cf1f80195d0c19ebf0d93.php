<?php $__env->startSection('title', __('crm.appointments_list')); ?>
<?php $__env->startSection('breadcrumb', __('crm.appointments')); ?>

<?php $__env->startSection('content'); ?>
<?php $filterKeys = ['search', 'status', 'appointment_type_id', 'assigned_to', 'date_from', 'date_to']; ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('crm.appointments_list')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('crm.appointments_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('appointments.create')); ?>" class="btn-primary">
        <i class="fa-solid fa-plus"></i>
        <?php echo e(__('crm.add_appointment')); ?>

    </a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="card px-5 py-4 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-calendar-days text-rose-600"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-slate-800"><?php echo e($appointments->total()); ?></p>
            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo e(__('crm.total_appointments')); ?></p>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route('appointments.index')); ?>" class="card px-5 py-4 mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <div>
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                   placeholder="<?php echo e(__('crm.appointment_title')); ?>..." class="form-input ps-10">
        </div>
    </div>
    <div>
        <select name="status" class="form-select w-full">
            <option value=""><?php echo e(__('app.all_statuses')); ?></option>
            <?php $__currentLoopData = \App\Models\Appointment::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($statusOption); ?>" <?php if(request('status') === $statusOption): echo 'selected'; endif; ?>><?php echo e(__('crm.status_' . $statusOption)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <select name="appointment_type_id" class="js-select2 form-select w-full">
            <option value=""><?php echo e(__('crm.all_types')); ?></option>
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($type->id); ?>" <?php if(request('appointment_type_id') == $type->id): echo 'selected'; endif; ?>><?php echo e($type->localized_name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <select name="assigned_to" class="js-select2 form-select w-full">
            <option value=""><?php echo e(__('crm.all_employees')); ?></option>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($employee->id); ?>" <?php if(request('assigned_to') == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-input w-full" title="<?php echo e(__('app.from')); ?>">
    </div>
    <div>
        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-input w-full" title="<?php echo e(__('app.to')); ?>">
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-filter"></i>
            <?php echo e(__('app.search')); ?>

        </button>
        <?php if(request()->hasAny($filterKeys)): ?>
            <a href="<?php echo e(route('appointments.index')); ?>" class="btn-secondary">
                <i class="fa-solid fa-xmark"></i>
                <?php echo e(__('app.clear_filters')); ?>

            </a>
        <?php endif; ?>
    </div>
</form>

<div class="card overflow-hidden">
    <?php if($appointments->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mb-5">
                <i class="fa-solid fa-calendar-days text-slate-400 text-3xl"></i>
            </div>
            <p class="text-slate-800 font-bold text-lg">
                <?php echo e(request()->hasAny($filterKeys) ? __('crm.no_appointments_search') : __('crm.no_appointments')); ?>

            </p>
            <?php if(!request()->hasAny($filterKeys)): ?>
                <a href="<?php echo e(route('appointments.create')); ?>" class="btn-primary mt-5">
                    <i class="fa-solid fa-plus"></i>
                    <?php echo e(__('crm.add_first_appointment')); ?>

                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('crm.appointment_title')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('crm.appointment_date')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('crm.appointment_type')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('crm.appointment_customer')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider hidden md:table-cell"><?php echo e(__('crm.appointment_assigned_to')); ?></th>
                        <th class="px-5 py-3.5 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('crm.appointment_status')); ?></th>
                        <th class="px-5 py-3.5 text-end text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.actions')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $dueToday = $appointment->isDueToday();
                        $overdue  = $appointment->isOverdue();
                        $rowClass = $dueToday ? 'bg-amber-50 hover:bg-amber-100' : ($overdue ? 'bg-rose-50 hover:bg-rose-100' : 'hover:bg-slate-50/50');
                    ?>
                    <tr class="transition-colors group <?php echo e($rowClass); ?>">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800"><?php echo e($appointment->title); ?></p>
                            <?php if($appointment->notes): ?>
                                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1"><?php echo e($appointment->notes); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">
                            <span dir="ltr"><?php echo e($appointment->appointment_date->format('Y-m-d')); ?></span>
                            <?php if($dueToday): ?>
                                <span class="badge bg-amber-100 text-amber-700 ms-1" title="<?php echo e(__('crm.due_today_hint')); ?>">
                                    <i class="fa-solid fa-bell"></i> <?php echo e(__('crm.due_today')); ?>

                                </span>
                            <?php elseif($overdue): ?>
                                <span class="badge bg-rose-100 text-rose-700 ms-1" title="<?php echo e(__('crm.overdue_hint')); ?>">
                                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo e(__('crm.overdue')); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4"><span class="badge bg-rose-100 text-rose-700"><?php echo e($appointment->type?->localized_name); ?></span></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600"><?php echo e($appointment->customer?->localized_name ?? '—'); ?></td>
                        <td class="px-5 py-4 hidden md:table-cell text-sm text-slate-600"><?php echo e($appointment->assignee?->name); ?></td>
                        <td class="px-5 py-4">
                            <?php if($appointment->status === 'completed'): ?>
                                <span class="badge bg-emerald-100 text-emerald-700"><?php echo e(__('crm.status_completed')); ?></span>
                            <?php else: ?>
                                <span class="badge bg-indigo-100 text-indigo-700"><?php echo e(__('crm.status_scheduled')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="<?php echo e(route('appointments.toggle-complete', $appointment)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit"
                                            title="<?php echo e($appointment->status === 'completed' ? __('crm.reopen') : __('crm.mark_completed')); ?>"
                                            class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                        <i class="fa-solid <?php echo e($appointment->status === 'completed' ? 'fa-rotate-left' : 'fa-check'); ?> text-sm"></i>
                                    </button>
                                </form>
                                <a href="<?php echo e(route('appointments.edit', $appointment)); ?>"
                                   class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="<?php echo e(__('app.edit')); ?>">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <button type="button" title="<?php echo e(__('app.delete')); ?>"
                                        @click="$dispatch('delete-confirm', { action: '<?php echo e(route('appointments.destroy', $appointment)); ?>', message: '<?php echo e(__('app.delete_confirm_msg')); ?>' })"
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

        <?php if($appointments->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            <p class="text-sm text-slate-500">
                <?php echo e(__('app.showing')); ?> <span class="font-bold text-slate-700"><?php echo e($appointments->firstItem()); ?></span>
                <?php echo e(__('app.to')); ?> <span class="font-bold text-slate-700"><?php echo e($appointments->lastItem()); ?></span>
                <?php echo e(__('app.of')); ?> <span class="font-bold text-slate-700"><?php echo e($appointments->total()); ?></span>
                <?php echo e(__('app.results')); ?>

            </p>
            <div class="flex gap-1">
                <?php if($appointments->onFirstPage()): ?>
                    <span class="btn btn-secondary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.previous')); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($appointments->previousPageUrl()); ?>" class="btn-secondary btn-sm"><?php echo e(__('app.previous')); ?></a>
                <?php endif; ?>
                <?php if($appointments->hasMorePages()): ?>
                    <a href="<?php echo e($appointments->nextPageUrl()); ?>" class="btn-primary btn-sm"><?php echo e(__('app.next')); ?></a>
                <?php else: ?>
                    <span class="btn btn-primary btn-sm opacity-40 !cursor-not-allowed"><?php echo e(__('app.next')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/crm/appointments/index.blade.php ENDPATH**/ ?>