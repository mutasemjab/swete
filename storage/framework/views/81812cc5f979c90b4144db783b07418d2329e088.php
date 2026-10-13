<?php $__env->startSection('title', __('maintenance.add_report')); ?>
<?php $__env->startSection('breadcrumb', __('maintenance.add_report')); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo e(__('maintenance.add_report')); ?></h1>
        <p class="page-subtitle"><?php echo e(__('maintenance.add_report_subtitle')); ?></p>
    </div>
    <a href="<?php echo e(route('maintenance-reports.index')); ?>" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        <?php echo e(__('app.back_to_list')); ?>

    </a>
</div>

<?php if($templates->isEmpty()): ?>
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-800 font-bold text-lg mb-4"><?php echo e(__('maintenance.no_templates_available')); ?></p>
        <a href="<?php echo e(route('report-templates.create')); ?>" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <?php echo e(__('maintenance.add_template')); ?>

        </a>
    </div>
<?php else: ?>
<form action="<?php echo e(route('maintenance-reports.store')); ?>" method="POST" enctype="multipart/form-data"
      x-data="{
        templates: <?php echo e($templates->map(fn ($t) => [
            'id'      => $t->id,
            'name'    => $t->localized_name,
            'product' => $t->material?->localized_name,
            'fields'  => $t->fields->map(fn ($f) => [
                'id' => $f->id, 'question' => $f->localized_question, 'type' => $f->type, 'options' => $f->options ?? [],
            ])->values(),
        ])->values()->toJson()); ?>,
        templateId: '<?php echo e(old('template_id')); ?>',
        get selectedTemplate() { return this.templates.find(t => String(t.id) === String(this.templateId)) || null; },
        materials: <?php echo e(collect(old('materials', []))->values()->toJson()); ?>,
        materialStock: <?php echo e($materialStock->toJson()); ?>,
        addMaterial() { this.materials.push({ material_id: '', quantity: '' }); this.$nextTick(() => window.initSelect2()); },
        removeMaterial(i) { this.materials.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">
    <?php echo csrf_field(); ?>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.add_report')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('maintenance.report_template')); ?> <span class="text-rose-500">*</span></label>
                <select name="template_id" x-model="templateId" class="js-select2 form-select <?php $__errorArgs = ['template_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('maintenance.report_select_template')); ?></option>
                    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($template->id); ?>" <?php if(old('template_id') == $template->id): echo 'selected'; endif; ?>><?php echo e($template->localized_name); ?> — <?php echo e($template->material?->localized_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['template_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

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
                        <option value="<?php echo e($customer->id); ?>" <?php if(old('customer_id') == $customer->id): echo 'selected'; endif; ?>><?php echo e($customer->localized_name); ?> (<?php echo e($customer->code); ?>)</option>
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
                <input type="date" name="date" value="<?php echo e(old('date', now()->toDateString())); ?>"
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
unset($__errorArgs, $__bag); ?>"><?php echo e(old('problem')); ?></textarea>
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
unset($__errorArgs, $__bag); ?>"><?php echo e(old('solution')); ?></textarea>
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
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes')); ?></textarea>
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

    <template x-if="selectedTemplate">
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-teal-500 text-sm"></i>
                    <?php echo e(__('maintenance.template_fields')); ?>

                </h3>
            </div>
            <div class="px-6 py-5 space-y-4">
                <template x-for="field in (selectedTemplate ? selectedTemplate.fields : [])" :key="field.id">
                    <div>
                        <label class="form-label" x-text="field.question"></label>

                        <template x-if="field.type === 'text'">
                            <input type="text" :name="`answers[${field.id}]`" class="form-input">
                        </template>
                        <template x-if="field.type === 'number'">
                            <input type="number" step="0.001" :name="`answers[${field.id}]`" dir="ltr" class="form-input">
                        </template>
                        <template x-if="field.type === 'boolean'">
                            <select :name="`answers[${field.id}]`" class="form-select">
                                <option value=""><?php echo e(__('app.select')); ?></option>
                                <option value="1"><?php echo e(__('app.yes')); ?></option>
                                <option value="0"><?php echo e(__('app.no')); ?></option>
                            </select>
                        </template>
                        <template x-if="field.type === 'choice'">
                            <select :name="`answers[${field.id}]`" class="form-select">
                                <option value=""><?php echo e(__('app.select')); ?></option>
                                <template x-for="opt in field.options" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                        </template>
                        <template x-if="field.type === 'images'">
                            <div>
                                <input type="file" :name="`answers[${field.id}][]`" multiple accept="image/*" class="form-input">
                                <p class="text-xs text-slate-400 mt-1"><?php echo e(__('maintenance.report_images_hint')); ?></p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.report_materials_used')); ?>

            </h3>
            <button type="button" @click="addMaterial()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('maintenance.report_add_material')); ?>

            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-xs text-slate-400 mb-4"><?php echo e(__('maintenance.report_materials_used_hint')); ?></p>
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
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?php echo e(__('maintenance.add_report')); ?>

        </button>
        <a href="<?php echo e(route('maintenance-reports.index')); ?>" class="btn-secondary"><?php echo e(__('app.cancel')); ?></a>
    </div>
</form>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/reports/create.blade.php ENDPATH**/ ?>