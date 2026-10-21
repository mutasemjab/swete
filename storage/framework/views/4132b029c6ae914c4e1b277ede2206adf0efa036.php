<?php $__env->startSection('title', $project->number); ?>
<?php $__env->startSection('breadcrumb', $project->number); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($project->number); ?>

            <?php $statusColor = ['active' => 'emerald', 'under_review' => 'amber', 'completed' => 'indigo', 'cancelled' => 'slate'][$project->status] ?? 'slate'; ?>
            <span class="badge bg-<?php echo e($statusColor); ?>-100 text-<?php echo e($statusColor); ?>-700"><?php echo e(__('tenders.project_status_' . $project->status)); ?></span>
            <span class="badge bg-purple-100 text-purple-700">P<?php echo e($project->variation_round); ?></span>
        </h1>
        <p class="page-subtitle"><?php echo e($project->localized_title); ?></p>
        <?php if($project->review_reason): ?>
            <p class="text-sm text-amber-600 mt-2"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo e(__('tenders.project_review_reason_notice')); ?> <?php echo e($project->review_reason); ?></p>
        <?php endif; ?>
    </div>
    <div class="flex items-center gap-2">
        <?php if($project->status === 'under_review'): ?>
            <form action="<?php echo e(route('project-reviews.approve', $project)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-check"></i>
                    <?php echo e(__('tenders.project_approve')); ?>

                </button>
            </form>
            <form action="<?php echo e(route('project-reviews.reject', $project)); ?>" method="POST"
                  @submit="if (! confirm('<?php echo e(__('tenders.project_reject_confirm')); ?>')) $event.preventDefault()">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-xmark"></i>
                    <?php echo e(__('tenders.project_reject')); ?>

                </button>
            </form>
        <?php endif; ?>
        <a href="<?php echo e(route('projects.edit', $project)); ?>" class="btn-secondary">
            <i class="fa-solid fa-pen"></i>
            <?php echo e(__('app.edit')); ?>

        </a>
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
            $canAssignTeam = auth()->id() === $project->manager_id;
            $canAssignManager = auth()->user()->can('projects.edit') || auth()->user()->can('tenders.projects.edit');
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
                                <?php if($assignment->notes): ?>
                                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e($assignment->notes); ?></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-slate-400"><?php echo e(__('tenders.quote_item_unassigned')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <?php if(! $assignment?->is_done): ?>
                                    <form action="<?php echo e(route('projects.item-assignments.store', $project)); ?>" method="POST" class="flex items-center gap-1.5">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="price_quote_item_id" value="<?php echo e($item->id); ?>">
                                        <select name="assigned_to" class="js-select2 form-select" style="min-width:140px">
                                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($employee->id); ?>" <?php if($assignment?->assigned_to == $employee->id): echo 'selected'; endif; ?>><?php echo e($employee->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <input type="text" name="notes" value="<?php echo e($assignment?->notes); ?>"
                                               placeholder="<?php echo e(__('tenders.quote_item_assignment_notes')); ?>" class="form-input" style="min-width:140px">
                                        <button type="submit" class="btn-secondary btn-sm"><?php echo e(__('tenders.quote_item_assign')); ?></button>
                                    </form>
                                <?php endif; ?>
                                <?php if($assignment && ! $assignment->is_done): ?>
                                    <form action="<?php echo e(route('projects.item-assignments.mark-done', [$project, $assignment])); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-primary btn-sm"><?php echo e(__('tenders.quote_item_mark_done')); ?></button>
                                    </form>
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
                <?php elseif(auth()->id() === $question->asked_to || auth()->user()->can('projects.edit') || auth()->user()->can('tenders.projects.edit')): ?>
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
    </div>
</div>

<div class="card overflow-hidden mb-5">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-slate-700"><?php echo e(__('tenders.project_agreements_section')); ?></h3>
        <a href="<?php echo e(route('agreements.create', ['project_id' => $project->id])); ?>" class="btn-secondary btn-sm">
            <i class="fa-solid fa-plus"></i>
            <?php echo e(__('tenders.add_project_agreement')); ?>

        </a>
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
                <form action="<?php echo e(route('projects.attachments.destroy', [$project, $attachment])); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-400 mb-4"><?php echo e(__('tenders.no_project_attachments')); ?></p>
        <?php endif; ?>

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
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/projects/show.blade.php ENDPATH**/ ?>