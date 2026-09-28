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

    <style type="text/tailwindcss">
        * { font-family: 'Cairo', sans-serif; -webkit-tap-highlight-color: transparent; }
        body { background: #f1f5f9; overscroll-behavior-y: contain; }
        [x-cloak] { display: none !important; }

        .app-bar  { @apply sticky top-0 z-20 bg-slate-900 text-white px-4 py-4 flex items-center gap-3 shadow-md; }
        .m-card   { @apply bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4; }
        .m-label  { @apply block text-sm font-bold text-slate-700 mb-2; }
        .m-input  { @apply w-full px-4 py-3.5 border border-slate-200 rounded-2xl text-base text-slate-800 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all; }
        .m-btn-primary { @apply w-full flex items-center justify-center gap-2 px-4 py-4 rounded-2xl font-bold text-base bg-indigo-600 text-white active:bg-indigo-800 shadow-lg shadow-indigo-500/30 disabled:opacity-40 transition-all; }
        .m-btn-outline { @apply flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-sm bg-white text-slate-700 border-2 border-slate-200 active:bg-slate-100 transition-all; }
        .m-chip { @apply w-9 h-9 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0; }
    </style>
</head>
<body class="min-h-screen pb-32">

    <div class="app-bar">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div>
            <p class="font-black leading-tight">{{ __('maintenance.mobile_new_report_title') }}</p>
            <p class="text-xs text-white/60">{{ config('app.name', 'ERP') }}</p>
        </div>
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
            addMaterial() { this.materials.push({ material_id: '', quantity: '' }); },
            removeMaterial(i) { this.materials.splice(i, 1); },
         }">

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
                <select name="template_id" x-model="templateId" class="m-input" required>
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
                <select name="customer_id" x-model="customerId" class="m-input mb-3" required>
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
                    <div class="space-y-4">
                        <template x-for="field in (selectedTemplate ? selectedTemplate.fields : [])" :key="field.id">
                            <div>
                                <label class="m-label" x-text="field.question"></label>
                                <template x-if="field.type === 'text'">
                                    <input type="text" :name="`answers[${field.id}]`" class="m-input">
                                </template>
                                <template x-if="field.type === 'number'">
                                    <input type="number" step="0.001" inputmode="decimal" :name="`answers[${field.id}]`" dir="ltr" class="m-input">
                                </template>
                                <template x-if="field.type === 'boolean'">
                                    <select :name="`answers[${field.id}]`" class="m-input">
                                        <option value="">{{ __('app.select') }}</option>
                                        <option value="1">{{ __('app.yes') }}</option>
                                        <option value="0">{{ __('app.no') }}</option>
                                    </select>
                                </template>
                                <template x-if="field.type === 'choice'">
                                    <select :name="`answers[${field.id}]`" class="m-input">
                                        <option value="">{{ __('app.select') }}</option>
                                        <template x-for="opt in field.options" :key="opt">
                                            <option :value="opt" x-text="opt"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="field.type === 'images'">
                                    <input type="file" :name="`answers[${field.id}][]`" multiple accept="image/*" capture="environment" class="m-input">
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
                            <select :name="`materials[${index}][material_id]`" x-model="row.material_id" class="m-input mb-2" required>
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
</body>
</html>
