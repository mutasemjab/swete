<?php $__env->startSection('title', $project->number); ?>
<?php $__env->startSection('breadcrumb', $project->number); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Shared across every mutating control on this page — every one of them is gated by this
    // AND $project->is_current, never by is_current alone (a viewer without edit rights should
    // never see a button that would 403 on click).
    $canEditProject = auth()->user()->can('projects.edit') || auth()->user()->can('tenders.projects.edit');
?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($project->number); ?>

            <?php $statusColor = ['active' => 'emerald', 'under_review' => 'amber', 'completed' => 'indigo', 'cancelled' => 'slate'][$project->status] ?? 'slate'; ?>
            <span class="badge bg-<?php echo e($statusColor); ?>-100 text-<?php echo e($statusColor); ?>-700"><?php echo e(__('tenders.project_status_' . $project->status)); ?></span>
            <?php if (! ($project->is_current)): ?>
                <span class="badge bg-slate-100 text-slate-500"><?php echo e(__('tenders.project_old_version_banner')); ?></span>
            <?php endif; ?>
        </h1>
        <p class="page-subtitle"><?php echo e($project->localized_title); ?></p>
        <?php if($project->review_reason): ?>
            <p class="text-sm text-amber-600 mt-2"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo e(__('tenders.project_review_reason_notice')); ?> <?php echo e($project->review_reason); ?></p>
        <?php endif; ?>
    </div>
    <div class="flex items-center gap-2">
        <?php if($project->is_current): ?>
            <?php if($project->status === 'under_review'): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('projects_under_review.approve')): ?>
                <form action="<?php echo e(route('project-reviews.approve', $project)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-check"></i>
                        <?php echo e(__('tenders.project_approve')); ?>

                    </button>
                </form>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('projects_under_review.reject')): ?>
                <form action="<?php echo e(route('project-reviews.reject', $project)); ?>" method="POST"
                      @submit="if (! confirm('<?php echo e(__('tenders.project_reject_confirm')); ?>')) $event.preventDefault()">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-danger">
                        <i class="fa-solid fa-xmark"></i>
                        <?php echo e(__('tenders.project_reject')); ?>

                    </button>
                </form>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($canEditProject): ?>
            <a href="<?php echo e(route('projects.edit', $project)); ?>" class="btn-secondary">
                <i class="fa-solid fa-pen"></i>
                <?php echo e(__('app.edit')); ?>

            </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo e(route('projects.show', $project->root_id)); ?>" class="btn-secondary">
                <?php echo e(__('tenders.project_view_current_version')); ?>

            </a>
        <?php endif; ?>
        <a href="<?php echo e($project->status === 'under_review' ? route('project-reviews.index') : route('projects.index')); ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
            <?php echo e(__('app.back_to_list')); ?>

        </a>
    </div>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_customer')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($project->customer?->localized_name ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_tender')); ?></dt>
            <dd class="font-bold text-slate-800">
                <?php if($project->tender): ?>
                    <a href="<?php echo e(route('tenders.show', $project->tender)); ?>" class="text-indigo-600 hover:underline"><?php echo e($project->tender->number); ?></a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_created_by')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($project->creator?->name ?? '—'); ?></dd>
        </div>
        <?php if($project->notes): ?>
        <div class="sm:col-span-3">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.tender_notes')); ?></dt>
            <dd class="text-slate-700"><?php echo e($project->notes); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-emerald-500 text-sm"></i>
            <?php echo e(__('tenders.financial_analysis_section')); ?>

        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-50 rounded-xl px-4 py-3">
            <p class="text-xs text-slate-400 font-medium mb-1"><?php echo e(__('tenders.financial_project_value')); ?></p>
            <p class="text-xl font-black text-slate-800" dir="ltr"><?php echo e(number_format($financialSummary['value'], 3)); ?></p>
        </div>
        <div class="bg-emerald-50 rounded-xl px-4 py-3">
            <p class="text-xs text-emerald-600 font-medium mb-1"><?php echo e(__('tenders.financial_paid')); ?></p>
            <p class="text-xl font-black text-emerald-700" dir="ltr"><?php echo e(number_format($financialSummary['paid'], 3)); ?></p>
        </div>
        <div class="bg-rose-50 rounded-xl px-4 py-3">
            <p class="text-xs text-rose-600 font-medium mb-1"><?php echo e(__('tenders.financial_due')); ?></p>
            <p class="text-xl font-black text-rose-700" dir="ltr"><?php echo e(number_format($financialSummary['due'], 3)); ?></p>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-users text-indigo-500 text-sm"></i>
            <?php echo e(__('tenders.project_team_section')); ?>

        </h3>
    </div>
    <div class="px-6 py-5">
        <?php
            $canAssignTeam = $project->is_current && auth()->id() === $project->manager_id;
            $canAssignManager = $project->is_current && (auth()->user()->can('projects.edit') || auth()->user()->can('tenders.projects.edit'));
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 text-sm">
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_manager')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e($project->manager?->name ?? '—'); ?></dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_engineer')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e($project->engineer?->name ?? '—'); ?></dd>
            </div>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('tenders.project_foreman')); ?></dt>
                <dd class="font-bold text-slate-800"><?php echo e($project->foreman?->name ?? '—'); ?></dd>
            </div>
        </div>
        <?php if($canAssignManager): ?>
        <form action="<?php echo e(route('projects.assign-manager', $project)); ?>" method="POST" class="flex items-end gap-3 flex-wrap pt-4 border-t border-slate-100">
            <?php echo csrf_field(); ?>
            <div class="flex-1 min-w-48">
                <label class="form-label"><?php echo e(__('tenders.project_manager')); ?></label>
                <select name="manager_id" class="js-select2 form-select">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>" <?php if($project->manager_id == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i>
                <?php echo e(__('app.save')); ?>

            </button>
        </form>
        <?php endif; ?>
        <?php if($canAssignTeam): ?>
        <form action="<?php echo e(route('projects.assign-team', $project)); ?>" method="POST" class="flex items-end gap-3 flex-wrap pt-4 border-t border-slate-100">
            <?php echo csrf_field(); ?>
            <div class="flex-1 min-w-48">
                <label class="form-label"><?php echo e(__('tenders.project_engineer')); ?></label>
                <select name="engineer_id" class="js-select2 form-select">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>" <?php if($project->engineer_id == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-1 min-w-48">
                <label class="form-label"><?php echo e(__('tenders.project_foreman')); ?></label>
                <select name="foreman_id" class="js-select2 form-select">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>" <?php if($project->foreman_id == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i>
                <?php echo e(__('app.save')); ?>

            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if($project->analyses->isNotEmpty()): ?>
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_linked_analyses_section')); ?></h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_number')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.analysis_branch')); ?></th>
                    <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('app.created_at')); ?></th>
                    <th class="px-5 py-3 w-20"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__currentLoopData = $project->analyses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $linkedAnalysis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-slate-50/50">
                    <td class="px-5 py-3 font-mono font-bold text-indigo-600">
                        <a href="<?php echo e(route('price-analyses.show', $linkedAnalysis)); ?>" class="hover:underline"><?php echo e($linkedAnalysis->number); ?></a>
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-600"><?php echo e($linkedAnalysis->branch?->localized_name); ?></td>
                    <td class="px-5 py-3 text-sm text-slate-600" dir="ltr"><?php echo e($linkedAnalysis->created_at->format('Y-m-d')); ?></td>
                    <td class="px-5 py-3 text-end">
                        <a href="<?php echo e(route('price-analyses.show', $linkedAnalysis)); ?>" class="btn-secondary btn-sm">
                            <i class="fa-solid fa-eye"></i> <?php echo e(__('app.view')); ?>

                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_quote_items_section')); ?></h3>
    </div>
    <?php if($quoteItems->isEmpty()): ?>
        <p class="px-6 py-6 text-sm text-slate-400"><?php echo e(__('tenders.no_project_quote_items')); ?></p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_quote_number')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_material')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_quantity')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_unit_price')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_assigned_to')); ?></th>
                        <th class="px-5 py-3 w-56"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $quoteItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $assignment = $item->assignments->firstWhere('project_id', $project->id); ?>
                    <tr class="transition-colors <?php echo e($assignment && ! $assignment->is_done ? 'bg-amber-50 hover:bg-amber-100' : ($assignment?->is_done ? 'bg-emerald-50 hover:bg-emerald-100' : 'hover:bg-slate-50/50')); ?>">
                        <td class="px-5 py-3 font-mono text-xs text-slate-500">
                            <?php echo e($item->quoteNumber); ?>

                            <?php if($item->voStatus === 'pending'): ?>
                                <span class="badge bg-amber-100 text-amber-700"><?php echo e(__('tenders.quote_pending_vo_badge')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-sm font-bold text-slate-800"><?php echo e($item->material?->localized_name ?? '—'); ?></td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr"><?php echo e(number_format($item->quantity, 3)); ?></td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr"><?php echo e(number_format($item->unit_price, 3)); ?></td>
                        <td class="px-5 py-3 text-sm">
                            <?php if($assignment): ?>
                                <span class="font-bold text-slate-700"><?php echo e($assignment->assignee?->name); ?></span>
                                <?php if($assignment->is_done): ?>
                                    <span class="badge bg-emerald-100 text-emerald-700 ms-1"><?php echo e(__('tenders.quote_item_done')); ?></span>
                                <?php endif; ?>
                                <div x-data="{ open: false }" class="mt-1">
                                    <button type="button" @click="open = true" class="text-xs text-indigo-600 hover:underline">
                                        <i class="fa-solid fa-comment-dots"></i>
                                        <?php echo e(__('tenders.view_comments')); ?> (<?php echo e($assignment->comments->count()); ?>)
                                    </button>
                                    <div x-show="open" x-cloak class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
                                        <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col">
                                            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                                                <h3 class="font-bold text-slate-800"><?php echo e(__('tenders.quote_item_comments_section')); ?></h3>
                                                <button type="button" @click="open = false" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                            <div class="px-6 py-4 overflow-y-auto flex-1 space-y-3">
                                                <?php $__empty_1 = true; $__currentLoopData = $assignment->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <div class="bg-slate-50 rounded-xl px-4 py-2.5">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="font-bold text-slate-800 text-sm"><?php echo e($comment->user?->name); ?></span>
                                                            <span class="text-xs text-slate-400" dir="ltr"><?php echo e($comment->created_at->format('Y-m-d H:i')); ?></span>
                                                        </div>
                                                        <p class="text-sm text-slate-700 mt-1"><?php echo e($comment->body); ?></p>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <p class="text-sm text-slate-400 text-center py-6"><?php echo e(__('tenders.no_comments')); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($project->is_current && ($canEditProject || auth()->id() === $assignment->assigned_to)): ?>
                                            <div class="px-6 py-4 border-t border-slate-100">
                                                <form action="<?php echo e(route('projects.item-assignments.comments.store', [$project, $assignment])); ?>" method="POST" class="flex items-end gap-2">
                                                    <?php echo csrf_field(); ?>
                                                    <textarea name="body" rows="2" required placeholder="<?php echo e(__('tenders.comment_placeholder')); ?>" class="form-input flex-1"></textarea>
                                                    <button type="submit" class="btn-primary btn-sm"><?php echo e(__('tenders.add_comment')); ?></button>
                                                </form>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-slate-400"><?php echo e(__('tenders.quote_item_unassigned')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <?php if($project->is_current): ?>
                                    <?php if(! $assignment?->is_done && $canEditProject): ?>
                                        <form action="<?php echo e(route('projects.item-assignments.store', $project)); ?>" method="POST" class="flex items-center gap-1.5">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="price_quote_item_id" value="<?php echo e($item->id); ?>">
                                            <select name="assigned_to" class="js-select2 form-select" style="min-width:140px">
                                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($employee->id); ?>" <?php if($assignment?->assigned_to == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <button type="submit" class="btn-secondary btn-sm"><?php echo e(__('tenders.quote_item_assign')); ?></button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if($assignment && ! $assignment->is_done && ($canEditProject || auth()->id() === $assignment->assigned_to)): ?>
                                        <form action="<?php echo e(route('projects.item-assignments.mark-done', [$project, $assignment])); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn-primary btn-sm"><?php echo e(__('tenders.quote_item_mark_done')); ?></button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_devices_section')); ?></h3>
    </div>
    <?php if($project->devices->isEmpty()): ?>
        <p class="px-6 py-6 text-sm text-slate-400"><?php echo e(__('tenders.no_devices')); ?></p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.quote_item_material')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.device_supply_date')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.device_installation_date')); ?></th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider"><?php echo e(__('tenders.device_warranty_start_date')); ?></th>
                        <th class="px-5 py-3 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $project->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50/50" x-data="{ editing: false }">
                        <td class="px-5 py-3 text-sm font-bold text-slate-800"><?php echo e($device->item?->material?->localized_name ?? '—'); ?></td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr" x-show="!editing"><?php echo e($device->supply_date?->format('Y-m-d') ?? '—'); ?></td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr" x-show="!editing"><?php echo e($device->installation_date?->format('Y-m-d') ?? '—'); ?></td>
                        <td class="px-5 py-3 text-sm text-slate-600" dir="ltr" x-show="!editing"><?php echo e($device->warranty_start_date?->format('Y-m-d') ?? '—'); ?></td>
                        <?php if($project->is_current && $canEditProject): ?>
                        <template x-if="editing">
                            <td colspan="4" class="px-5 py-3">
                                <form action="<?php echo e(route('projects.devices.store', $project)); ?>" method="POST" class="flex items-end gap-2 flex-wrap">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="price_quote_item_id" value="<?php echo e($device->price_quote_item_id); ?>">
                                    <div>
                                        <label class="form-label"><?php echo e(__('tenders.device_supply_date')); ?></label>
                                        <input type="date" name="supply_date" value="<?php echo e($device->supply_date?->format('Y-m-d')); ?>" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label"><?php echo e(__('tenders.device_installation_date')); ?></label>
                                        <input type="date" name="installation_date" value="<?php echo e($device->installation_date?->format('Y-m-d')); ?>" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label"><?php echo e(__('tenders.device_warranty_start_date')); ?></label>
                                        <input type="date" name="warranty_start_date" value="<?php echo e($device->warranty_start_date?->format('Y-m-d')); ?>" class="form-input">
                                    </div>
                                    <button type="submit" class="btn-primary btn-sm"><?php echo e(__('app.save')); ?></button>
                                    <button type="button" @click="editing = false" class="btn-secondary btn-sm"><?php echo e(__('app.cancel')); ?></button>
                                </form>
                            </td>
                        </template>
                        <?php endif; ?>
                        <td class="px-5 py-3 text-end" x-show="!editing">
                            <?php if($project->is_current && $canEditProject): ?>
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="editing = true" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all">
                                        <i class="fa-solid fa-pen text-sm"></i>
                                    </button>
                                    <form action="<?php echo e(route('projects.devices.destroy', [$project, $device])); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if($project->is_current && $canEditProject): ?>
        <?php $undevicedItems = $quoteItems->whereNotIn('id', $project->devices->pluck('price_quote_item_id')); ?>
        <?php if($undevicedItems->isNotEmpty()): ?>
            <form action="<?php echo e(route('projects.devices.store', $project)); ?>" method="POST" class="px-6 py-4 border-t border-slate-100 flex items-end gap-2 flex-wrap">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="form-label"><?php echo e(__('tenders.quote_item_material')); ?></label>
                    <select name="price_quote_item_id" class="js-select2 form-select" style="min-width:220px" required>
                        <?php $__currentLoopData = $undevicedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->material?->localized_name ?? '—'); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="form-label"><?php echo e(__('tenders.device_supply_date')); ?></label>
                    <input type="date" name="supply_date" class="form-input">
                </div>
                <div>
                    <label class="form-label"><?php echo e(__('tenders.device_installation_date')); ?></label>
                    <input type="date" name="installation_date" class="form-input">
                </div>
                <div>
                    <label class="form-label"><?php echo e(__('tenders.device_warranty_start_date')); ?></label>
                    <input type="date" name="warranty_start_date" class="form-input">
                </div>
                <button type="submit" class="btn-primary btn-sm"><?php echo e(__('tenders.add_device')); ?></button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_questions_section')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $project->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="py-3 border-b border-slate-100 last:border-0">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-sm text-slate-800"><?php echo e($question->question); ?></p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <?php echo e(__('tenders.question_target_employee')); ?>: <span class="font-bold"><?php echo e($question->askedTo?->name ?? __('app.all')); ?></span>
                            · <?php echo e($question->askedBy?->name); ?>

                        </p>
                    </div>
                </div>
                <?php if($question->answer): ?>
                    <div class="mt-2 bg-emerald-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-emerald-600 font-bold mb-0.5"><?php echo e(__('tenders.question_answer')); ?></p>
                        <p class="text-sm text-slate-700"><?php echo e($question->answer); ?></p>
                    </div>
                <?php elseif($project->is_current && ($canEditProject || auth()->id() === $question->asked_to)): ?>
                    <form action="<?php echo e(route('projects.questions.answer', [$project, $question])); ?>" method="POST" class="mt-2 flex items-end gap-2 flex-wrap">
                        <?php echo csrf_field(); ?>
                        <div class="flex-1 min-w-48">
                            <input type="text" name="answer" class="form-input" placeholder="<?php echo e(__('tenders.question_answer')); ?>" required>
                        </div>
                        <button type="submit" class="btn-secondary btn-sm"><?php echo e(__('tenders.answer_question')); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('tenders.no_questions')); ?></p>
        <?php endif; ?>

        <?php if($project->is_current && $canEditProject): ?>
        <form action="<?php echo e(route('projects.questions.store', $project)); ?>" method="POST" class="mt-4 pt-4 border-t border-slate-100 flex items-end gap-3 flex-wrap">
            <?php echo csrf_field(); ?>
            <div class="flex-1 min-w-56">
                <label class="form-label"><?php echo e(__('tenders.add_question')); ?></label>
                <input type="text" name="question" class="form-input" required>
            </div>
            <div class="flex-1 min-w-40">
                <label class="form-label"><?php echo e(__('tenders.question_target_employee')); ?></label>
                <select name="asked_to" class="js-select2 form-select">
                    <option value=""><?php echo e(__('app.all')); ?></option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('tenders.add_question')); ?>

            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_agreements_section')); ?></h3>
        <?php if($project->is_current): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('agreements.create')): ?>
            <a href="<?php echo e(route('agreements.create', ['project_id' => $project->id])); ?>" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('tenders.add_project_agreement')); ?>

            </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $project->agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="<?php echo e($agreement->url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-file-contract"></i>
                    <?php echo e($agreement->number); ?>

                </a>
                <span class="text-xs text-slate-400"><?php echo e($agreement->created_at->format('Y-m-d')); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400"><?php echo e(__('tenders.no_project_agreements')); ?></p>
        <?php endif; ?>
    </div>
</div>



<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_attachments')); ?></h3>
    </div>
    <div class="px-6 py-5">
        <?php $__empty_1 = true; $__currentLoopData = $project->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <a href="<?php echo e($attachment->url); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-sm flex items-center gap-2">
                    <i class="fa-solid fa-paperclip"></i>
                    <?php echo e($attachment->name); ?>

                </a>
                <?php if($project->is_current && $canEditProject): ?>
                <form action="<?php echo e(route('projects.attachments.destroy', [$project, $attachment])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('tenders.no_project_attachments')); ?></p>
        <?php endif; ?>

        <?php if($project->is_current && $canEditProject): ?>
        <form action="<?php echo e(route('projects.attachments.store', $project)); ?>" method="POST" enctype="multipart/form-data"
              class="mt-4 pt-4 border-t border-slate-100 space-y-3"
              x-data="{
                nextId: 1,
                rows: [{ id: 0 }],
                addRow() { this.rows.push({ id: this.nextId++ }); },
                removeRow(id) { if (this.rows.length > 1) this.rows = this.rows.filter(r => r.id !== id); },
              }">
            <?php echo csrf_field(); ?>
            <template x-for="row in rows" :key="row.id">
                <div class="flex items-end gap-3 flex-wrap">
                    <div class="flex-1 min-w-40">
                        <label class="form-label"><?php echo e(__('tenders.project_attachment_name')); ?></label>
                        <input type="text" :name="`attachments[${row.id}][name]`" class="form-input">
                    </div>
                    <div class="flex-1 min-w-56">
                        <label class="form-label"><?php echo e(__('tenders.project_attachment_file')); ?></label>
                        <input type="file" :name="`attachments[${row.id}][file]`" class="form-input">
                    </div>
                    <button type="button" @click="removeRow(row.id)"
                            class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all flex-shrink-0">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </div>
            </template>
            <div class="flex items-center gap-3">
                <button type="button" @click="addRow()" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-plus"></i>
                    <?php echo e(__('tenders.project_attachment_add_row')); ?>

                </button>
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fa-solid fa-upload"></i>
                    <?php echo e(__('tenders.project_attachment_upload')); ?>

                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if($project->is_current && $project->versions->isNotEmpty()): ?>
<div class="card overflow-hidden mt-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_version_history')); ?></h3>
    </div>
    <div class="px-6 py-4">
        <?php $__currentLoopData = $project->versions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oldVersion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <span class="text-sm text-slate-600">
                    <span class="font-mono font-bold" dir="ltr"><?php echo e($oldVersion->number); ?></span>
                    — <span dir="ltr"><?php echo e($oldVersion->updated_at->format('Y-m-d H:i')); ?></span>
                </span>
                <a href="<?php echo e(route('projects.show', $oldVersion)); ?>" class="btn-secondary btn-sm">
                    <i class="fa-solid fa-eye"></i> <?php echo e(__('app.view')); ?>

                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/projects/show.blade.php ENDPATH**/ ?>