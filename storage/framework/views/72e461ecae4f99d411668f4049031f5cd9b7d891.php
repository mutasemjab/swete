
<div x-data="{
        open: false,
        saving: false,
        error: '',
        name: '',
        submit() {
            this.saving = true;
            this.error = '';
            fetch('<?php echo e($storeRoute); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ name: this.name }),
            })
                .then(async (res) => {
                    if (!res.ok) throw await res.json();
                    return res.json();
                })
                .then((data) => {
                    const select = document.getElementById('<?php echo e($targetSelectId); ?>');
                    const option = new Option(data.name + ' (' + data.code + ')', data.id, true, true);
                    select.append(option);
                    $(select).trigger('change');
                    this.open = false;
                    this.name = '';
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
            <h3 class="text-lg font-black text-slate-800 mb-4"><?php echo e($label); ?></h3>
            <label class="form-label"><?php echo e(__('accounting.party_name')); ?></label>
            <input type="text" x-model="name" @keydown.enter.prevent="submit()" class="form-input">
            <p class="form-error" x-show="error" x-text="error"></p>
            <div class="flex gap-3 mt-5">
                <button type="button" @click="open = false" class="btn-secondary flex-1"><?php echo e(__('app.cancel')); ?></button>
                <button type="button" :disabled="saving || !name" @click="submit()" class="btn-primary flex-1">
                    <span x-show="!saving"><?php echo e(__('app.save')); ?></span>
                    <span x-show="saving">…</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/components/party-quick-add-modal.blade.php ENDPATH**/ ?>