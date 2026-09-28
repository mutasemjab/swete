@extends('layouts.app')

@section('title', __('maintenance.add_report'))
@section('breadcrumb', __('maintenance.add_report'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_report') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.add_report_subtitle') }}</p>
    </div>
    <a href="{{ route('maintenance-reports.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

@if($templates->isEmpty())
    <div class="card px-6 py-10 text-center">
        <p class="text-slate-800 font-bold text-lg mb-4">{{ __('maintenance.no_templates_available') }}</p>
        <a href="{{ route('report-templates.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            {{ __('maintenance.add_template') }}
        </a>
    </div>
@else
<form action="{{ route('maintenance-reports.store') }}" method="POST" enctype="multipart/form-data"
      x-data="{
        templates: {{ $templates->map(fn ($t) => [
            'id'      => $t->id,
            'name'    => $t->localized_name,
            'product' => $t->material?->localized_name,
            'fields'  => $t->fields->map(fn ($f) => [
                'id' => $f->id, 'question' => $f->localized_question, 'type' => $f->type, 'options' => $f->options ?? [],
            ])->values(),
        ])->values()->toJson() }},
        templateId: '{{ old('template_id') }}',
        get selectedTemplate() { return this.templates.find(t => String(t.id) === String(this.templateId)) || null; },
        materials: {{ collect(old('materials', []))->values()->toJson() }},
        materialStock: {{ $materialStock->toJson() }},
        addMaterial() { this.materials.push({ material_id: '', quantity: '' }); this.$nextTick(() => window.initSelect2()); },
        removeMaterial(i) { this.materials.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">
    @csrf

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-teal-500 text-sm"></i>
                {{ __('maintenance.add_report') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('maintenance.report_template') }} <span class="text-rose-500">*</span></label>
                <select name="template_id" x-model="templateId" class="js-select2 form-select @error('template_id') is-invalid @enderror">
                    <option value="">{{ __('maintenance.report_select_template') }}</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>{{ $template->localized_name }} — {{ $template->material?->localized_name }}</option>
                    @endforeach
                </select>
                @error('template_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_customer') }} <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_problem') }}</label>
                <textarea name="problem" rows="2" class="form-input @error('problem') is-invalid @enderror">{{ old('problem') }}</textarea>
                @error('problem')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_solution') }}</label>
                <textarea name="solution" rows="2" class="form-input @error('solution') is-invalid @enderror">{{ old('solution') }}</textarea>
                @error('solution')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('maintenance.report_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <template x-if="selectedTemplate">
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-teal-500 text-sm"></i>
                    {{ __('maintenance.template_fields') }}
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
                                <option value="">{{ __('app.select') }}</option>
                                <option value="1">{{ __('app.yes') }}</option>
                                <option value="0">{{ __('app.no') }}</option>
                            </select>
                        </template>
                        <template x-if="field.type === 'choice'">
                            <select :name="`answers[${field.id}]`" class="form-select">
                                <option value="">{{ __('app.select') }}</option>
                                <template x-for="opt in field.options" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                        </template>
                        <template x-if="field.type === 'images'">
                            <div>
                                <input type="file" :name="`answers[${field.id}][]`" multiple accept="image/*" class="form-input">
                                <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.report_images_hint') }}</p>
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
                {{ __('maintenance.report_materials_used') }}
            </h3>
            <button type="button" @click="addMaterial()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                {{ __('maintenance.report_add_material') }}
            </button>
        </div>
        <div class="px-6 py-5">
            <p class="text-xs text-slate-400 mb-4">{{ __('maintenance.report_materials_used_hint') }}</p>
            <template x-if="materials.length === 0">
                <p class="text-sm text-slate-400">{{ __('maintenance.report_no_materials') }}</p>
            </template>
            <div class="space-y-3">
                <template x-for="(row, index) in materials" :key="index">
                    <div class="flex items-start gap-3">
                        <div class="flex-1">
                            <select :name="`materials[${index}][material_id]`" x-model="row.material_id" class="js-select2 form-select" required>
                                <option value="">{{ __('maintenance.report_material') }}</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->localized_name }} ({{ $material->code }})</option>
                                @endforeach
                            </select>
                            <template x-if="row.material_id">
                                <p class="mt-1 text-[11px] text-slate-500">
                                    <i class="fa-solid fa-warehouse text-slate-400"></i>
                                    {{ __('maintenance.report_current_stock') }}:
                                    <span class="font-bold text-slate-700" x-text="materialStock[row.material_id] || 0"></span>
                                </p>
                            </template>
                        </div>
                        <div class="w-32">
                            <input type="number" :name="`materials[${index}][quantity]`" x-model="row.quantity"
                                   step="0.001" min="0.001" dir="ltr" class="form-input" placeholder="{{ __('maintenance.report_quantity') }}" required>
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
            {{ __('maintenance.add_report') }}
        </button>
        <a href="{{ route('maintenance-reports.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endif
@endsection
