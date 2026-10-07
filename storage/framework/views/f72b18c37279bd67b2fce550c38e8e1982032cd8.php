<?php
    $isRtl = app()->isLocale('ar');
    $dir   = $isRtl ? 'rtl' : 'ltr';
    $lang  = app()->getLocale();
?>
<!DOCTYPE html>
<html lang="<?php echo e($lang); ?>" dir="<?php echo e($dir); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#0f172a">
    <title><?php echo e(__('maintenance.mobile_visit_title')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <?php echo $__env->make('maintenance.visits._mobile-styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="min-h-screen pb-36">

    <div class="app-bar">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="flex-1">
            <p class="font-black leading-tight"><?php echo e($visit->customer?->localized_name); ?></p>
            <p class="text-xs text-white/60" dir="ltr"><?php echo e($visit->check_in_at->format('Y-m-d H:i')); ?></p>
        </div>
        <form method="POST" action="<?php echo e(route('auth.logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 active:bg-white/20" title="<?php echo e(__('app.logout')); ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        </form>
    </div>

    <div class="max-w-lg mx-auto px-4 pt-4"
         x-data="{
            visitId: <?php echo e($visit->id); ?>,
            csrf: document.querySelector('meta[name=csrf-token]').content,
            templates: <?php echo e($templates->map(fn ($t) => ['id' => $t->id, 'name' => $t->localized_name . ' — ' . $t->material?->localized_name])->values()->toJson()); ?>,
            newTemplateId: '',
            adding: false,
            finishError: <?php echo e(session('error') ? json_encode(session('error')) : 'null'); ?>,
            materialsList: <?php echo e($materials->map(fn ($m) => ['id' => $m->id, 'label' => $m->localized_name . ' (' . $m->code . ')'])->values()->toJson()); ?>,
            materialStock: <?php echo e($materialStock->toJson()); ?>,
            reports: <?php echo e($visit->reports->map(fn ($r) => [
                'id'       => $r->id,
                'number'   => $r->number,
                'name'     => $r->template_name,
                'problem'  => $r->problem,
                'solution' => $r->solution,
                'notes'    => $r->notes,
                '_metaStatus' => 'idle',
                'fields'   => $r->fields->map(fn ($f) => [
                    'id' => $f->id, 'question' => $f->localized_question, 'type' => $f->type,
                    'options' => $f->options ?? [], 'answer' => $f->answer,
                    // Root-relative, NOT asset()-ified — must match the JS-side '/' + path
                    // convention used after a live upload/removal round-trip (see uploadImage()/
                    // removeImage() below), so a path can always be stripped back for comparison.
                    'imageUrls' => $f->type === 'images' && $f->answer
                        ? collect(json_decode($f->answer, true) ?: [])->map(fn ($p) => '/' . $p)->values()
                        : [],
                    '_status' => 'idle',
                ])->values(),
                'materials' => $r->materials->map(fn ($m) => ['material_id' => (string) $m->material_id, 'quantity' => (float) $m->quantity])->values(),
            ])->values()->toJson()); ?>,

            addReport() {
                if (! this.newTemplateId) return;
                this.adding = true;
                fetch(`/m/visits/${this.visitId}/reports`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ template_id: this.newTemplateId }),
                }).then(r => r.json()).then(data => {
                    this.reports.push({
                        id: data.report.id, number: data.report.number, name: data.report.name,
                        problem: '', solution: '', notes: '', _metaStatus: 'idle',
                        fields: data.fields.map(f => ({ ...f, answer: f.answer ?? '', imageUrls: [], _status: 'idle' })),
                        materials: [],
                    });
                    this.newTemplateId = '';
                    this.adding = false;
                    this.$nextTick(() => window.initSelect2());
                }).catch(() => { this.adding = false; });
            },

            saveAnswer(report, field) {
                field._status = 'saving';
                fetch(`/m/visits/${this.visitId}/reports/${report.id}/answer`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ field_id: field.id, answer: field.answer }),
                }).then(r => r.json()).then(data => { field._status = data.ok ? 'saved' : 'error'; })
                  .catch(() => { field._status = 'error'; });
            },

            uploadImage(report, field, event) {
                Array.from(event.target.files).forEach(file => {
                    field._status = 'saving';
                    const fd = new FormData();
                    fd.append('field_id', field.id);
                    fd.append('image', file);
                    fd.append('_method', 'PATCH');
                    fetch(`/m/visits/${this.visitId}/reports/${report.id}/answer`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: fd,
                    }).then(r => r.json()).then(data => {
                        if (data.ok) {
                            field.answer = data.answer;
                            field.imageUrls = JSON.parse(data.answer || '[]').map(p => '/' + p);
                            field._status = 'saved';
                        } else {
                            field._status = 'error';
                        }
                    }).catch(() => { field._status = 'error'; });
                });
                event.target.value = '';
            },

            removeImage(report, field, path) {
                field._status = 'saving';
                const relativePath = path.replace(/^\//, '');
                fetch(`/m/visits/${this.visitId}/reports/${report.id}/answer`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ field_id: field.id, remove_path: relativePath }),
                }).then(r => r.json()).then(data => {
                    field.answer = data.answer;
                    field.imageUrls = data.answer ? JSON.parse(data.answer).map(p => '/' + p) : [];
                    field._status = 'saved';
                }).catch(() => { field._status = 'error'; });
            },

            saveReportMeta(report) {
                report._metaStatus = 'saving';
                fetch(`/m/visits/${this.visitId}/reports/${report.id}`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ problem: report.problem, solution: report.solution, notes: report.notes }),
                }).then(r => r.json()).then(data => { report._metaStatus = data.ok ? 'saved' : 'error'; })
                  .catch(() => { report._metaStatus = 'error'; });
            },

            addMaterialRow(report) {
                report.materials.push({ material_id: '', quantity: '' });
                this.$nextTick(() => window.initSelect2());
            },

            removeMaterialRow(report, index) {
                report.materials.splice(index, 1);
                this.syncMaterials(report);
            },

            syncMaterials(report) {
                const rows = report.materials.filter(r => r.material_id && r.quantity);
                fetch(`/m/visits/${this.visitId}/reports/${report.id}/materials`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ materials: rows }),
                }).catch(() => {});
            },
         }"
         x-init="$nextTick(() => window.initSelect2())">

        <template x-if="finishError">
            <div class="m-card !bg-rose-50 !border-rose-200 text-center py-4 mb-4" x-cloak>
                <p class="font-bold text-rose-700" x-text="finishError"></p>
            </div>
        </template>

        
        <template x-for="(report, rIndex) in reports" :key="report.id">
            <div class="m-card">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-black text-slate-800" x-text="report.name"></p>
                    <span class="text-[11px] font-mono text-slate-400" dir="ltr" x-text="report.number"></span>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <template x-for="field in report.fields" :key="field.id">
                        <div :class="field.type === 'images' ? 'col-span-2' : ''">
                            <label class="m-label flex items-center gap-1.5">
                                <span x-text="field.question"></span>
                                <i class="fa-solid fa-circle-check m-save-ok text-xs" x-show="field._status === 'saved'"></i>
                                <i class="fa-solid fa-spinner fa-spin m-save-pending text-xs" x-show="field._status === 'saving'"></i>
                                <i class="fa-solid fa-triangle-exclamation m-save-err text-xs" x-show="field._status === 'error'" :title="'<?php echo e(__('app.save_failed')); ?>'"></i>
                            </label>

                            <template x-if="field.type === 'text'">
                                <input type="text" x-model="field.answer" class="m-input"
                                       @input.debounce.600ms="saveAnswer(report, field)" @blur="saveAnswer(report, field)">
                            </template>

                            <template x-if="field.type === 'number'">
                                <input type="number" step="0.001" inputmode="decimal" dir="ltr" x-model="field.answer" class="m-input"
                                       @input.debounce.600ms="saveAnswer(report, field)" @blur="saveAnswer(report, field)">
                            </template>

                            <template x-if="field.type === 'boolean'">
                                <select x-model="field.answer" class="js-select2 m-input" @change="saveAnswer(report, field)">
                                    <option value=""><?php echo e(__('app.select')); ?></option>
                                    <option value="1"><?php echo e(__('app.yes')); ?></option>
                                    <option value="0"><?php echo e(__('app.no')); ?></option>
                                </select>
                            </template>

                            <template x-if="field.type === 'choice'">
                                <select x-model="field.answer" class="js-select2 m-input" @change="saveAnswer(report, field)">
                                    <option value=""><?php echo e(__('app.select')); ?></option>
                                    <template x-for="opt in field.options" :key="opt">
                                        <option :value="opt" x-text="opt"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'images'">
                                <div>
                                    <input type="file" multiple accept="image/*" class="m-input" @change="uploadImage(report, field, $event)">
                                    <p class="text-[10px] text-slate-400 mt-1"><?php echo e(__('maintenance.report_images_hint')); ?></p>
                                    <div class="flex flex-wrap gap-2 mt-2" x-show="field.imageUrls.length > 0">
                                        <template x-for="url in field.imageUrls" :key="url">
                                            <div class="relative w-16 h-16 flex-shrink-0">
                                                <img :src="url" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                                                <button type="button" @click="removeImage(report, field, url)"
                                                        class="absolute -top-1.5 -end-1.5 w-5 h-5 bg-rose-600 text-white rounded-full flex items-center justify-center text-[10px] shadow">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="mb-3">
                    <label class="m-label flex items-center gap-1.5">
                        <?php echo e(__('maintenance.report_problem')); ?>

                        <i class="fa-solid fa-circle-check m-save-ok text-xs" x-show="report._metaStatus === 'saved'"></i>
                    </label>
                    <textarea rows="2" x-model="report.problem" class="m-input mb-2"
                              @input.debounce.600ms="saveReportMeta(report)" @blur="saveReportMeta(report)"></textarea>
                    <label class="m-label"><?php echo e(__('maintenance.report_solution')); ?></label>
                    <textarea rows="2" x-model="report.solution" class="m-input"
                              @input.debounce.600ms="saveReportMeta(report)" @blur="saveReportMeta(report)"></textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-sm text-slate-700"><?php echo e(__('maintenance.report_materials_used')); ?></p>
                        <button type="button" @click="addMaterialRow(report)" class="m-btn-outline !py-1.5 !px-2.5">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <template x-if="report.materials.length === 0">
                        <p class="text-xs text-slate-400"><?php echo e(__('maintenance.report_no_materials')); ?></p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="(row, mIndex) in report.materials" :key="mIndex">
                            <div class="border border-slate-200 rounded-2xl p-2.5">
                                <select x-model="row.material_id" class="js-select2 m-input mb-2" @change="syncMaterials(report)">
                                    <option value=""><?php echo e(__('maintenance.report_material')); ?></option>
                                    <template x-for="m in materialsList" :key="m.id">
                                        <option :value="m.id" x-text="m.label"></option>
                                    </template>
                                </select>
                                <div class="flex items-center gap-2">
                                    <input type="number" step="0.001" min="0.001" inputmode="decimal" dir="ltr" x-model="row.quantity"
                                           class="m-input flex-1" placeholder="<?php echo e(__('maintenance.report_quantity')); ?>"
                                           @input.debounce.600ms="syncMaterials(report)" @blur="syncMaterials(report)">
                                    <button type="button" @click="removeMaterialRow(report, mIndex)" class="w-11 h-11 flex-shrink-0 flex items-center justify-center text-rose-500 bg-rose-50 rounded-2xl">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        
        <div class="m-card">
            <p class="font-black text-slate-800 mb-3"><?php echo e(__('maintenance.visit_add_template')); ?></p>
            <div class="flex items-center gap-2">
                <select x-model="newTemplateId" class="js-select2 m-input flex-1">
                    <option value=""><?php echo e(__('maintenance.report_select_template')); ?></option>
                    <template x-for="t in templates" :key="t.id">
                        <option :value="t.id" x-text="t.name"></option>
                    </template>
                </select>
                <button type="button" @click="addReport()" :disabled="!newTemplateId || adding" class="m-btn-outline !px-4">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="fixed inset-x-0 bottom-0 z-20 bg-white/95 backdrop-blur border-t border-slate-200 px-4 py-3 flex gap-2" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
            <div class="max-w-lg mx-auto w-full flex gap-2">
                <form :action="`/m/visits/${visitId}/finish`" method="POST" class="flex-1" onsubmit="return confirm('<?php echo e(__('maintenance.visit_finish_confirm')); ?>')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="m-btn-primary !bg-emerald-600 shadow-emerald-500/30">
                        <i class="fa-solid fa-paper-plane"></i>
                        <?php echo e(__('maintenance.visit_finish')); ?>

                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php echo $__env->make('maintenance.visits._mobile-select2-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/visits/mobile-show.blade.php ENDPATH**/ ?>