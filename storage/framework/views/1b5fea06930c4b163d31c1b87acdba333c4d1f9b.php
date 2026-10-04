
<div x-data="{
        open: false,
        saving: false,
        error: '',
        name: '',
        categoryId: '',
        unitId: '',
        submit() {
            this.saving = true;
            this.error = '';
            fetch('<?php echo e(route('warehouse.materials.quick-store')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    name: this.name,
                    category_id: this.categoryId,
                    unit_id: this.unitId,
                }),
            })
                .then(async (res) => {
                    if (!res.ok) throw await res.json();
                    return res.json();
                })
                .then((data) => {
                    document.querySelectorAll('<?php echo e($targetSelector); ?>').forEach((select) => {
                        const option = new Option(data.name + ' (' + data.code + ')', data.id);
                        select.appendChild(option);
                        $(select).trigger('change.select2');
                    });
                    this.open = false;
                    this.name = '';
                    this.categoryId = '';
                    this.unitId = '';
                })
                .catch((err) => { this.error = err.message || '<?php echo e(__('app.error_occurred')); ?>'; })
                .finally(() => { this.saving = false; });
        },
     }"
     class="inline-block">
    <button type="button" @click="open = true" class="btn-secondary btn-sm">
        <i class="fa-solid fa-plus"></i>
        <?php echo e($label); ?>

    </button>

    <div x-show="open" x-cloak
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         @keydown.escape.window="open = false">
        <div @click.outside="open = false" class="bg-white rounded-3xl shadow-2xl shadow-slate-900/20 p-6 max-w-sm w-full">
            <h3 class="text-lg font-black text-slate-800 mb-1.5"><?php echo e($label); ?></h3>
            <p class="text-xs text-slate-400 mb-4"><?php echo e(__('warehouse.add_material_quick_hint')); ?></p>

            <label class="form-label"><?php echo e(__('warehouse.material_name')); ?></label>
            <input type="text" x-model="name" @keydown.enter.prevent="submit()" class="form-input mb-3">

            <label class="form-label"><?php echo e(__('warehouse.material_category')); ?></label>
            <select x-model="categoryId" class="form-select mb-3">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->path); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <label class="form-label"><?php echo e(__('warehouse.material_unit')); ?></label>
            <select x-model="unitId" class="form-select">
                <option value=""><?php echo e(__('app.select')); ?></option>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($unit->id); ?>"><?php echo e($unit->localized_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <p class="form-error" x-show="error" x-text="error"></p>
            <div class="flex gap-3 mt-5">
                <button type="button" @click="open = false" class="btn-secondary flex-1"><?php echo e(__('app.cancel')); ?></button>
                <button type="button" :disabled="saving || !name || !categoryId || !unitId" @click="submit()" class="btn-primary flex-1">
                    <span x-show="!saving"><?php echo e(__('app.save')); ?></span>
                    <span x-show="saving">…</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/components/material-quick-add-modal.blade.php ENDPATH**/ ?>