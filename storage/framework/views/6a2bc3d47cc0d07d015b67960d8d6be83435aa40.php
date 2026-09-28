<?php $__env->startSection('title', __('maintenance.edit_report')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.edit_report')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.edit_report')); ?></h1>
        <p class="page-subtitle"><?php echo e($report->number); ?> — <?php echo e($report->template_name); ?></p>
    </div>
    <a href="<?php echo e(route('maintenance-reports.show', $report)); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<form action="<?php echo e(route('maintenance-reports.update', $report)); ?>" method="POST" enctype="multipart/form-data"
      x-data="{
        materials: <?php echo e(collect(old('materials', $report->materials->map(fn ($m) => ['material_id' => $m->material_id, 'quantity' => (float) $m->quantity])))->values()->toJson()); ?>,
        materialStock: <?php echo e($materialStock->toJson()); ?>,
        addMaterial() { this.materials.push({ material_id: '', quantity: '' }); this.$nextTick(() => window.initSelect2()); },
        removeMaterial(i) { this.materials.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.edit_report')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('maintenance.report_customer')); ?> <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="js-select2 form-select <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($customer->id); ?>" <?php if(old('customer_id', $report->customer_id) == $customer->id): echo 'selected'; endif; ?>><?php echo e($customer->localized_name); ?> (<?php echo e($customer->code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('maintenance.report_date')); ?> <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="<?php echo e(old('date', $report->date->toDateString())); ?>"
                       class="form-input <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('maintenance.report_problem')); ?></label>
                <textarea name="problem" rows="2" class="form-input <?php $__errorArgs = ['problem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('problem', $report->problem)); ?></textarea>
                <?php $__errorArgs = ['problem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('maintenance.report_solution')); ?></label>
                <textarea name="solution" rows="2" class="form-input <?php $__errorArgs = ['solution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('solution', $report->solution)); ?></textarea>
                <?php $__errorArgs = ['solution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label"><?php echo e(__('maintenance.report_notes')); ?></label>
                <textarea name="notes" rows="2" class="form-input <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $report->notes)); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.template_fields')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 space-y-4">
            <?php $__currentLoopData = $report->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <label class="form-label"><?php echo e($field->localized_question); ?></label>

                <?php if($field->type === 'text'): ?>
                    <input type="text" name="answers[<?php echo e($field->id); ?>]" value="<?php echo e(old("answers.{$field->id}", $field->answer)); ?>"
                           class="form-input <?php $__errorArgs = ["answers.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php elseif($field->type === 'number'): ?>
                    <input type="number" step="0.001" dir="ltr" name="answers[<?php echo e($field->id); ?>]" value="<?php echo e(old("answers.{$field->id}", $field->answer)); ?>"
                           class="form-input <?php $__errorArgs = ["answers.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php elseif($field->type === 'boolean'): ?>
                    <select name="answers[<?php echo e($field->id); ?>]" class="form-select <?php $__errorArgs = ["answers.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value=""><?php echo e(__('app.select')); ?></option>
                        <option value="1" <?php if(old("answers.{$field->id}", $field->answer) === '1'): echo 'selected'; endif; ?>><?php echo e(__('app.yes')); ?></option>
                        <option value="0" <?php if(old("answers.{$field->id}", $field->answer) === '0'): echo 'selected'; endif; ?>><?php echo e(__('app.no')); ?></option>
                    </select>
                <?php elseif($field->type === 'choice'): ?>
                    <select name="answers[<?php echo e($field->id); ?>]" class="form-select <?php $__errorArgs = ["answers.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value=""><?php echo e(__('app.select')); ?></option>
                        <?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option); ?>" <?php if(old("answers.{$field->id}", $field->answer) === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php elseif($field->type === 'images'): ?>
                    <?php if($field->image_urls): ?>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <?php $__currentLoopData = $field->image_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e($url); ?>" target="_blank"><img src="<?php echo e($url); ?>" class="w-16 h-16 object-cover rounded-lg border border-slate-200"></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 mb-2"><?php echo e(__('maintenance.report_no_images')); ?></p>
                    <?php endif; ?>
                    <input type="file" name="answers[<?php echo e($field->id); ?>][]" multiple accept="image/*"
                           class="form-input <?php $__errorArgs = ["answers.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <p class="text-xs text-slate-400 mt-1"><?php echo e(__('maintenance.report_existing_images_hint')); ?></p>
                <?php endif; ?>
                <?php $__errorArgs = ["answers.{$field->id}"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.report_materials_used')); ?>

            </h3>
            <?php if($report->materials_approval_status === 'none'): ?>
            <button type="button" @click="addMaterial()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('maintenance.report_add_material')); ?>

            </button>
            <?php endif; ?>
        </div>
        <div class="px-6 py-5">
            <?php if($report->materials_approval_status !== 'none'): ?>
                <p class="text-xs text-amber-600 mb-4"><i class="fa-solid fa-lock"></i> <?php echo e(__('maintenance.report_materials_locked_hint')); ?></p>
                <?php if($report->materials->isEmpty()): ?>
                    <p class="text-sm text-slate-400"><?php echo e(__('maintenance.report_no_materials')); ?></p>
                <?php else: ?>
                    <ul class="text-sm text-slate-700 space-y-1">
                        <?php $__currentLoopData = $report->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($item->material?->localized_name); ?> — <span class="font-bold" dir="ltr"><?php echo e(number_format($item->quantity, 3)); ?></span></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <template x-if="materials.length === 0">
                    <p class="text-sm text-slate-400"><?php echo e(__('maintenance.report_no_materials')); ?></p>
                </template>
                <div class="space-y-3">
                    <template x-for="(row, index) in materials" :key="index">
                        <div class="flex items-start gap-3">
                            <div class="flex-1">
                                <select :name="`materials[${index}][material_id]`" x-model="row.material_id" class="js-select2 form-select" required>
                                    <option value=""><?php echo e(__('maintenance.report_material')); ?></option>
                                    <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($material->id); ?>"><?php echo e($material->localized_name); ?> (<?php echo e($material->code); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <template x-if="row.material_id">
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        <i class="fa-solid fa-warehouse text-slate-400"></i>
                                        <?php echo e(__('maintenance.report_current_stock')); ?>:
                                        <span class="font-bold text-slate-700" x-text="materialStock[row.material_id] || 0"></span>
                                    </p>
                                </template>
                            </div>
                            <div class="w-32">
                                <input type="number" :name="`materials[${index}][quantity]`" x-model="row.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input" placeholder="<?php echo e(__('maintenance.report_quantity')); ?>" required>
                            </div>
                            <button type="button" @click="removeMaterial(index)" class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('app.save')); ?>

        </button>
        <a href="<?php echo e(route('maintenance-reports.show', $report)); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/reports/edit.blade.php ENDPATH**/ ?>