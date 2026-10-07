<?php $__env->startSection('title', __('maintenance.visit_details')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.visit_details')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <?php echo e($visit->customer?->localized_name); ?>

            <?php echo $__env->make('maintenance.visits._status-badge', ['status' => $visit->status], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </h1>
        <p class="page-subtitle"><?php echo e($visit->technician?->name); ?> — <span dir="ltr"><?php echo e($visit->check_in_at->format('Y-m-d H:i')); ?></span></p>
    </div>
    <a href="<?php echo e(route('maintenance-visits.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_check_in_at')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e($visit->check_in_at->format('Y-m-d H:i')); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_check_out_at')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e($visit->check_out_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_duration')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e($visit->duration ?? '—'); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_signed_at')); ?></dt>
            <dd class="font-bold text-slate-800" dir="ltr"><?php echo e($visit->signed_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
        </div>
        <?php if($visit->signature_path): ?>
        <div class="sm:col-span-4">
            <dt class="text-slate-400 font-medium mb-1"><?php echo e(__('maintenance.visit_signature')); ?></dt>
            <dd><img src="<?php echo e($visit->signature_url); ?>" class="h-20 border border-slate-200 rounded-lg bg-white"></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>

<?php $__currentLoopData = $visit->reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="card overflow-hidden mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e($report->template_name); ?></h3>
        <span class="text-xs font-mono text-slate-400" dir="ltr"><?php echo e($report->number); ?></span>
    </div>
    <div class="px-6 py-5 space-y-4 text-sm">
        <?php if($report->problem): ?>
        <div><dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_problem')); ?></dt><dd class="text-slate-700"><?php echo e($report->problem); ?></dd></div>
        <?php endif; ?>
        <?php if($report->solution): ?>
        <div><dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.report_solution')); ?></dt><dd class="text-slate-700"><?php echo e($report->solution); ?></dd></div>
        <?php endif; ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <?php $__currentLoopData = $report->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <dt class="text-slate-400 font-medium mb-0.5"><?php echo e($field->localized_question); ?></dt>
                <?php if($field->type === 'images'): ?>
                    <dd class="flex flex-wrap gap-2 mt-1">
                        <?php $__empty_1 = true; $__currentLoopData = $field->image_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($url); ?>" target="_blank"><img src="<?php echo e($url); ?>" class="w-16 h-16 object-cover rounded-lg border border-slate-200"></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="text-slate-400">—</span>
                        <?php endif; ?>
                    </dd>
                <?php else: ?>
                    <dd class="font-semibold text-slate-700"><?php echo e($field->formatted_answer ?? '—'); ?></dd>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($report->materials->isNotEmpty()): ?>
        <div>
            <dt class="text-slate-400 font-medium mb-1"><?php echo e(__('maintenance.report_materials_used')); ?></dt>
            <dd class="space-y-1">
                <?php $__currentLoopData = $report->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="font-semibold"><?php echo e($material->material?->localized_name); ?></span>
                        <span class="text-slate-400">×</span>
                        <span dir="ltr"><?php echo e($material->quantity); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </dd>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($visit->status === 'customer_signed'): ?>
<div class="card mb-5" x-data="{
        visitTypeId: '',
        types: <?php echo e($visitTypes->map(fn ($t) => ['id' => $t->id, 'name' => $t->localized_name, 'requiresNote' => $t->requires_note])->values()->toJson()); ?>,
        get requiresNote() { const t = this.types.find(x => String(x.id) === String(this.visitTypeId)); return t ? t.requiresNote : false; },
     }">
    <div class="card-header">
        <h3 class="font-bold text-slate-700"><?php echo e(__('maintenance.visit_classify')); ?></h3>
    </div>
    <form action="<?php echo e(route('maintenance-visits.classify', $visit)); ?>" method="POST" class="px-6 py-5 space-y-4">
        <?php echo csrf_field(); ?>
        <div>
            <label class="form-label"><?php echo e(__('maintenance.visit_type')); ?> <span class="text-rose-500">*</span></label>
            <select name="visit_type_id" x-model="visitTypeId" class="form-select <?php $__errorArgs = ['visit_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $visitTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visitType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($visitType->id); ?>"><?php echo e($visitType->localized_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['visit_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div x-show="requiresNote" x-cloak>
            <label class="form-label"><?php echo e(__('maintenance.visit_classification_note')); ?></label>
            <textarea name="note" rows="3" class="form-input <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('note')); ?></textarea>
            <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-check"></i>
            <?php echo e(__('maintenance.visit_mark_done')); ?>

        </button>
    </form>
</div>
<?php elseif($visit->status === 'closed'): ?>
<div class="card px-6 py-5 mb-5">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_type')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($visit->visitType?->localized_name); ?></dd>
        </div>
        <div>
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_classified_by')); ?></dt>
            <dd class="font-bold text-slate-800"><?php echo e($visit->classifiedBy?->name); ?></dd>
        </div>
        <?php if($visit->classification_note): ?>
        <div class="sm:col-span-2">
            <dt class="text-slate-400 font-medium mb-0.5"><?php echo e(__('maintenance.visit_classification_note')); ?></dt>
            <dd class="text-slate-700"><?php echo e($visit->classification_note); ?></dd>
        </div>
        <?php endif; ?>
    </dl>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/visits/show.blade.php ENDPATH**/ ?>