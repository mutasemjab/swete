<?php $template = $template ?? null; ?>
<div x-data="{
        fields: <?php echo e((
            $template?->fields->map(fn ($f) => [
                'question' => $f->question, 'question_en' => $f->question_en, 'type' => $f->type,
                'options' => $f->options ?: [''],
            ])->values()
            ?? collect([['question' => '', 'question_en' => '', 'type' => 'text', 'options' => ['']]])
        )->toJson()); ?>,
        addField() { this.fields.push({ question: '', question_en: '', type: 'text', options: [''] }); },
        removeField(i) { if (this.fields.length > 1) this.fields.splice(i, 1); },
      }">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.templates_list')); ?>

            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label"><?php echo e(__('maintenance.template_name')); ?> <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name', $template?->name)); ?>"
                       class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('app.name_en')); ?></label>
                <input type="text" name="name_en" value="<?php echo e(old('name_en', $template?->name_en)); ?>" dir="ltr"
                       class="form-input <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="form-label"><?php echo e(__('maintenance.template_product')); ?> <span class="text-rose-500">*</span></label>
                <select name="material_id" class="js-select2 form-select <?php $__errorArgs = ['material_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($material->id); ?>" <?php if(old('material_id', $template?->material_id) == $material->id): echo 'selected'; endif; ?>><?php echo e($material->localized_name); ?> (<?php echo e($material->code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['material_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if($template): ?>
            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="sr-only peer" <?php if(old('status', $template->status)): echo 'checked'; endif; ?>>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                                peer-checked:bg-indigo-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700"><?php echo e(__('app.active')); ?></span>
                </label>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-teal-500 text-sm"></i>
                <?php echo e(__('maintenance.template_fields')); ?>

            </h3>
            <button type="button" @click="addField()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                <?php echo e(__('maintenance.template_add_field')); ?>

            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <template x-for="(field, index) in fields" :key="index">
                <div class="border border-slate-200 rounded-xl p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <label class="form-label"><?php echo e(__('maintenance.template_field_question')); ?></label>
                            <input type="text" :name="`fields[${index}][question]`" x-model="field.question" class="form-input" required>
                        </div>
                        <div class="sm:col-span-1">
                            <label class="form-label"><?php echo e(__('app.name_en')); ?></label>
                            <input type="text" :name="`fields[${index}][question_en]`" x-model="field.question_en" dir="ltr" class="form-input">
                        </div>
                        <div class="sm:col-span-1">
                            <label class="form-label"><?php echo e(__('maintenance.template_field_type')); ?></label>
                            <select :name="`fields[${index}][type]`" x-model="field.type" class="form-select">
                                <option value="text"><?php echo e(__('maintenance.answer_type_text')); ?></option>
                                <option value="number"><?php echo e(__('maintenance.answer_type_number')); ?></option>
                                <option value="boolean"><?php echo e(__('maintenance.answer_type_boolean')); ?></option>
                                <option value="choice"><?php echo e(__('maintenance.answer_type_choice')); ?></option>
                                <option value="images"><?php echo e(__('maintenance.answer_type_images')); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3" x-show="field.type === 'choice'">
                        <label class="form-label"><?php echo e(__('maintenance.template_field_options')); ?></label>
                        <div class="space-y-1.5">
                            <template x-for="(option, oIndex) in field.options" :key="oIndex">
                                <div class="flex items-center gap-1.5">
                                    <input type="text" :name="`fields[${index}][options][${oIndex}]`" x-model="field.options[oIndex]"
                                           class="form-input !py-1.5 !text-sm">
                                    <button type="button" @click="field.options.splice(oIndex, 1)"
                                            class="p-1 text-slate-300 hover:text-rose-600 flex-shrink-0">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="field.options.push('')"
                                    class="text-xs font-semibold text-indigo-600 hover:underline">
                                <i class="fa-solid fa-plus"></i> <?php echo e(__('maintenance.template_field_add_option')); ?>

                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end mt-3">
                        <button type="button" @click="removeField(index)"
                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
        <?php $__errorArgs = ['fields'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/report-templates/_form.blade.php ENDPATH**/ ?>