@extends('layouts.app')

@section('title', __('maintenance.edit_report'))
@section('breadcrumb', __('maintenance.edit_report'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_report') }}</h1>
        <p class="page-subtitle">{{ $report->number }} — {{ $report->template_name }}</p>
    </div>
    <a href="{{ route('maintenance-reports.show', $report) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-reports.update', $report) }}" method="POST" enctype="multipart/form-data"
      x-data="{
        materials: {{ collect(old('materials', $report->materials->map(fn ($m) => ['material_id' => $m->material_id, 'quantity' => (float) $m->quantity])))->values()->toJson() }},
        materialStock: {{ $materialStock->toJson() }},
        addMaterial() { this.materials.push({ material_id: '', quantity: '' }); this.$nextTick(() => window.initSelect2()); },
        removeMaterial(i) { this.materials.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-teal-500 text-sm"></i>
                {{ __('maintenance.edit_report') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('maintenance.report_customer') }} <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $report->customer_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', $report->date->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_problem') }}</label>
                <textarea name="problem" rows="2" class="form-input @error('problem') is-invalid @enderror">{{ old('problem', $report->problem) }}</textarea>
                @error('problem')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('maintenance.report_solution') }}</label>
                <textarea name="solution" rows="2" class="form-input @error('solution') is-invalid @enderror">{{ old('solution', $report->solution) }}</textarea>
                @error('solution')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('maintenance.report_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $report->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-teal-500 text-sm"></i>
                {{ __('maintenance.template_fields') }}
            </h3>
        </div>
        <div class="px-6 py-5 space-y-4">
            @foreach($report->fields as $field)
            <div>
                <label class="form-label">{{ $field->localized_question }}</label>

                @if($field->type === 'text')
                    <input type="text" name="answers[{{ $field->id }}]" value="{{ old("answers.{$field->id}", $field->answer) }}"
                           class="form-input @error("answers.{$field->id}") is-invalid @enderror">
                @elseif($field->type === 'number')
                    <input type="number" step="0.001" dir="ltr" name="answers[{{ $field->id }}]" value="{{ old("answers.{$field->id}", $field->answer) }}"
                           class="form-input @error("answers.{$field->id}") is-invalid @enderror">
                @elseif($field->type === 'boolean')
                    <select name="answers[{{ $field->id }}]" class="form-select @error("answers.{$field->id}") is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        <option value="1" @selected(old("answers.{$field->id}", $field->answer) === '1')>{{ __('app.yes') }}</option>
                        <option value="0" @selected(old("answers.{$field->id}", $field->answer) === '0')>{{ __('app.no') }}</option>
                    </select>
                @elseif($field->type === 'choice')
                    <select name="answers[{{ $field->id }}]" class="form-select @error("answers.{$field->id}") is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach($field->options ?? [] as $option)
                            <option value="{{ $option }}" @selected(old("answers.{$field->id}", $field->answer) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                @elseif($field->type === 'images')
                    @if($field->image_urls)
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($field->image_urls as $url)
                                <a href="{{ $url }}" target="_blank"><img src="{{ $url }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200"></a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 mb-2">{{ __('maintenance.report_no_images') }}</p>
                    @endif
                    <input type="file" name="answers[{{ $field->id }}][]" multiple accept="image/*"
                           class="form-input @error("answers.{$field->id}") is-invalid @enderror">
                    <p class="text-xs text-slate-400 mt-1">{{ __('maintenance.report_existing_images_hint') }}</p>
                @endif
                @error("answers.{$field->id}")<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            @endforeach
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-teal-500 text-sm"></i>
                {{ __('maintenance.report_materials_used') }}
            </h3>
            @if($report->materials_approval_status === 'none')
            <button type="button" @click="addMaterial()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                {{ __('maintenance.report_add_material') }}
            </button>
            @endif
        </div>
        <div class="px-6 py-5">
            @if($report->materials_approval_status !== 'none')
                <p class="text-xs text-amber-600 mb-4"><i class="fa-solid fa-lock"></i> {{ __('maintenance.report_materials_locked_hint') }}</p>
                @if($report->materials->isEmpty())
                    <p class="text-sm text-slate-400">{{ __('maintenance.report_no_materials') }}</p>
                @else
                    <ul class="text-sm text-slate-700 space-y-1">
                        @foreach($report->materials as $item)
                            <li>{{ $item->material?->localized_name }} — <span class="font-bold" dir="ltr">{{ number_format($item->quantity, 3) }}</span></li>
                        @endforeach
                    </ul>
                @endif
            @else
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
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('maintenance-reports.show', $report) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
