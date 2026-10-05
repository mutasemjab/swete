@php
    $isRtl = app()->isLocale('ar');
    $dir   = $isRtl ? 'rtl' : 'ltr';
    $lang  = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>{{ __('maintenance.mobile_new_report_title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    {{-- Search-and-select for every picker on this page, touch-tuned — same upgrade mechanism as
         the main app's layout (add class="js-select2" to any <select>), reimplemented standalone
         here since this page intentionally doesn't extend layouts.app. --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <style type="text/tailwindcss">
        * { font-family: 'Cairo', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: #f1f5f9; overscroll-behavior-y: contain; }
        [x-cloak] { display: none !important; }

        .app-bar  { @apply sticky top-0 z-20 bg-slate-900 text-white px-4 py-4 flex items-center gap-3 shadow-md; }
        .m-card   { @apply bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4; }
        .m-label  { @apply block text-sm font-bold text-slate-700 mb-2; }
        .m-input  { @apply w-full px-4 py-3.5 border border-slate-200 rounded-2xl text-base text-slate-800 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all; }
        .m-input-sm { @apply w-full px-2 py-2.5 border border-slate-200 rounded-xl text-[13px] leading-tight text-slate-800 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all; }
        .m-label-sm { @apply block text-[11px] font-bold text-slate-500 mb-1 leading-tight; }
        .m-btn-primary { @apply w-full flex items-center justify-center gap-2 px-4 py-4 rounded-2xl font-bold text-base bg-indigo-600 text-white active:bg-indigo-800 shadow-lg shadow-indigo-500/30 disabled:opacity-40 transition-all; }
        .m-btn-outline { @apply flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-sm bg-white text-slate-700 border-2 border-slate-200 active:bg-slate-100 transition-all; }
        .m-chip { @apply w-9 h-9 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0; }
    </style>

    {{-- Select2, restyled to match .m-input/.m-input-sm and sized for a comfortable thumb tap target. --}}
    <style>
        .select2-container--default .select2-selection--single {
            height: 52px; border: 1px solid #e2e8f0; border-radius: 1rem;
            display: flex; align-items: center; padding: 0 1rem; background: #f8fafc;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #818cf8; box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2);
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding: 0; font-size: 1rem; color: #1e293b; line-height: 1.25rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #94a3b8; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 50px; }
        [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow { left: 0.75rem; right: auto; }
        .select2-dropdown { border-radius: 1rem; border-color: #e2e8f0; overflow: hidden; }
        .select2-search--dropdown { padding: 0.5rem; }
        .select2-search--dropdown .select2-search__field {
            border-radius: 0.75rem; border-color: #e2e8f0; padding: 0.6rem 0.75rem; outline: none; font-size: 1rem;
        }
        .select2-results__option { padding: 0.65rem 0.75rem; font-size: 0.95rem; }
        .select2-results__option--highlighted[aria-selected] { background-color: #4f46e5 !important; }
        /* Compact variant for the 3-per-row template fields */
        .m-select2-sm .select2-selection--single { height: 40px !important; border-radius: 0.75rem !important; padding: 0 0.5rem !important; }
        .m-select2-sm .select2-selection__rendered { font-size: 12.5px !important; }
        .m-select2-sm .select2-selection__arrow { height: 38px !important; }
    </style>
</head>
<body class="min-h-screen pb-32">

    <div class="app-bar">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="flex-1">
            <p class="font-black leading-tight">{{ __('maintenance.mobile_new_report_title') }}</p>
            <p class="text-xs text-white/60">{{ config('app.name', 'ERP') }}</p>
        </div>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 active:bg-white/20" title="{{ __('app.logout') }}">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        </form>
    </div>

    <div class="max-w-lg mx-auto px-4 pt-4"
         x-data="{
            success: {{ session('success') ? 'true' : 'false' }},
            templates: {{ $templates->map(fn ($t) => [
                'id'      => $t->id,
                'name'    => $t->localized_name,
                'product' => $t->material?->localized_name,
                'fields'  => $t->fields->map(fn ($f) => [
                    'id' => $f->id, 'question' => $f->localized_question, 'type' => $f->type, 'options' => $f->options ?? [],
                ])->values(),
            ])->values()->toJson() }},
            templateId: '',
            customerId: '',
            get selectedTemplate() { return this.templates.find(t => String(t.id) === String(this.templateId)) || null; },
            materials: [],
            materialStock: {{ $materialStock->toJson() }},
            addMaterial() { this.materials.push({ material_id: '', quantity: '' }); this.$nextTick(() => window.initSelect2()); },
            removeMaterial(i) { this.materials.splice(i, 1); },
            imageFiles: {},
            onImagesSelected(fieldId, event) {
                if (! this.imageFiles[fieldId]) this.imageFiles[fieldId] = [];
                Array.from(event.target.files).forEach(f => this.imageFiles[fieldId].push({ file: f, url: URL.createObjectURL(f) }));
                this.syncImagesInput(fieldId);
            },
            removeImage(fieldId, idx) {
                URL.revokeObjectURL(this.imageFiles[fieldId][idx].url);
                this.imageFiles[fieldId].splice(idx, 1);
                this.syncImagesInput(fieldId);
            },
            syncImagesInput(fieldId) {
                const input = document.getElementById('images-input-' + fieldId);
                if (! input) return;
                const dt = new DataTransfer();
                (this.imageFiles[fieldId] || []).forEach(item => dt.items.add(item.file));
                input.files = dt.files;
            },
         }"
         x-init="
            $nextTick(() => window.initSelect2());
            $watch('templateId', () => $nextTick(() => window.initSelect2()));
         ">

        <template x-if="success">
            <div class="m-card !bg-emerald-50 !border-emerald-200 text-center py-8" x-cloak>
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-check text-emerald-600 text-2xl"></i>
                </div>
                <p class="font-black text-emerald-800 text-lg mb-4">{{ __('maintenance.mobile_report_sent') }}</p>
                <a href="{{ route('maintenance-reports.mobile.create') }}" class="m-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('maintenance.mobile_add_another') }}
                </a>
            </div>
        </template>

        <form action="{{ route('maintenance-reports.mobile.store') }}" method="POST" enctype="multipart/form-data" x-show="!success">
            @csrf

            <div class="m-card">
                <div class="flex items-center gap-2 mb-3">
                    <span class="m-chip bg-indigo-600 text-white">1</span>
                    <p class="font-black text-slate-800">{{ __('maintenance.mobile_choose_template_step') }}</p>
                </div>
                <select name="template_id" x-model="templateId" class="js-select2 m-input" required>
                    <option value="">{{ __('maintenance.report_select_template') }}</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->localized_name }} — {{ $template->material?->localized_name }}</option>
                    @endforeach
                </select>
                @error('template_id')<p class="text-rose-500 text-xs mt-2 font-bold">{{ $message }}</p>@enderror
            </div>

            <div class="m-card">
                <div class="flex items-center gap-2 mb-3">
                    <span class="m-chip bg-indigo-600 text-white">2</span>
                    <p class="font-black text-slate-800">{{ __('maintenance.mobile_choose_customer_step') }}</p>
                </div>
                <select name="customer_id" x-model="customerId" class="js-select2 m-input mb-3" required>
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                <label class="m-label">{{ __('maintenance.report_date') }}</label>
                <input type="date" name="date" value="{{ now()->toDateString() }}" class="m-input" required>
            </div>

            <template x-if="selectedTemplate">
                <div class="m-card" x-cloak>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="m-chip bg-indigo-600 text-white">3</span>
                        <p class="font-black text-slate-800">{{ __('maintenance.template_fields') }}</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="field in (selectedTemplate ? selectedTemplate.fields : [])" :key="field.id">
                            <div :class="field.type === 'images' ? 'col-span-3' : ''">
                                <label class="m-label-sm" x-text="field.question"></label>
                                <template x-if="field.type === 'text'">
                                    <input type="text" :name="`answers[${field.id}]`" class="m-input-sm">
                                </template>
                                <template x-if="field.type === 'number'">
                                    <input type="number" step="0.001" inputmode="decimal" :name="`answers[${field.id}]`" dir="ltr" class="m-input-sm">
                                </template>
                                <template x-if="field.type === 'boolean'">
                                    <select :name="`answers[${field.id}]`" class="js-select2 m-select2-sm">
                                        <option value="">{{ __('app.select') }}</option>
                                        <option value="1">{{ __('app.yes') }}</option>
                                        <option value="0">{{ __('app.no') }}</option>
                                    </select>
                                </template>
                                <template x-if="field.type === 'choice'">
                                    <select :name="`answers[${field.id}]`" class="js-select2 m-select2-sm">
                                        <option value="">{{ __('app.select') }}</option>
                                        <template x-for="opt in field.options" :key="opt">
                                            <option :value="opt" x-text="opt"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="field.type === 'images'">
                                    <div>
                                        <input type="file" :id="'images-input-' + field.id" :name="`answers[${field.id}][]`"
                                               multiple accept="image/*" class="m-input-sm" @change="onImagesSelected(field.id, $event)">
                                        <p class="text-[10px] text-slate-400 mt-1">{{ __('maintenance.report_images_hint') }}</p>
                                        <div class="flex flex-wrap gap-2 mt-2" x-show="(imageFiles[field.id] || []).length > 0">
                                            <template x-for="(img, idx) in (imageFiles[field.id] || [])" :key="idx">
                                                <div class="relative w-16 h-16 flex-shrink-0">
                                                    <img :src="img.url" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                                                    <button type="button" @click="removeImage(field.id, idx)"
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
                </div>
            </template>

            <div class="m-card">
                <p class="font-black text-slate-800 mb-3">{{ __('maintenance.report_problem') }} / {{ __('maintenance.report_solution') }}</p>
                <label class="m-label">{{ __('maintenance.report_problem') }}</label>
                <textarea name="problem" rows="3" class="m-input mb-3"></textarea>
                <label class="m-label">{{ __('maintenance.report_solution') }}</label>
                <textarea name="solution" rows="3" class="m-input"></textarea>
            </div>

            <div class="m-card">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-black text-slate-800">{{ __('maintenance.report_materials_used') }}</p>
                    <button type="button" @click="addMaterial()" class="m-btn-outline !py-2 !px-3">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <template x-if="materials.length === 0">
                    <p class="text-sm text-slate-400">{{ __('maintenance.report_no_materials') }}</p>
                </template>
                <div class="space-y-3">
                    <template x-for="(row, index) in materials" :key="index">
                        <div class="border border-slate-200 rounded-2xl p-3">
                            <select :name="`materials[${index}][material_id]`" x-model="row.material_id" class="js-select2 m-input mb-2" required>
                                <option value="">{{ __('maintenance.report_material') }}</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->localized_name }} ({{ $material->code }})</option>
                                @endforeach
                            </select>
                            <template x-if="row.material_id">
                                <p class="text-xs text-slate-500 mb-2">
                                    {{ __('maintenance.report_current_stock') }}:
                                    <span class="font-bold" x-text="materialStock[row.material_id] || 0"></span>
                                </p>
                            </template>
                            <div class="flex items-center gap-2">
                                <input type="number" :name="`materials[${index}][quantity]`" x-model="row.quantity"
                                       step="0.001" min="0.001" inputmode="decimal" dir="ltr" class="m-input flex-1" placeholder="{{ __('maintenance.report_quantity') }}" required>
                                <button type="button" @click="removeMaterial(index)" class="w-12 h-12 flex-shrink-0 flex items-center justify-center text-rose-500 bg-rose-50 rounded-2xl">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="fixed inset-x-0 bottom-0 z-20 bg-white/95 backdrop-blur border-t border-slate-200 px-4 py-3" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
                <div class="max-w-lg mx-auto">
                    <button type="submit" class="m-btn-primary">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ __('maintenance.mobile_submit_report') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.initSelect2 = function (context) {
            (context ? $(context) : $(document)).find('.js-select2').each(function () {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) return;
                $el.select2({
                    dir: '{{ $isRtl ? "rtl" : "ltr" }}',
                    width: '100%',
                    dropdownAutoWidth: false,
                    placeholder: $el.data('placeholder') || $el.find('option[value=""]').first().text() || '',
                    allowClear: $el.find('option[value=""]').length > 0 && !$el.prop('required'),
                });
                // See layouts/app.blade.php for why this bridge exists: select2's own change
                // events don't always reliably reach a vanilla-listener framework like Alpine.
                $el.on('select2:select select2:unselect select2:clear', function () {
                    this.dispatchEvent(new Event('change'));
                });
            });
        };
        document.addEventListener('DOMContentLoaded', () => window.initSelect2());
    </script>
</body>
</html>
