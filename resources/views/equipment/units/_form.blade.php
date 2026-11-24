@php
    $unit = $unit ?? null;
    $selectedManufacturerId = old('equipment_manufacturer_id', $unit?->model?->equipment_manufacturer_id);
@endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-server text-cyan-500 text-sm"></i>
            {{ __('equipment.unit') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('equipment.unit_customer') }} <span class="text-rose-500">*</span></label>
            <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id', $unit?->customer_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                @endforeach
            </select>
            @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.unit_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $unit?->name) }}" placeholder="{{ __('equipment.unit_name_hint') }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $unit?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.manufacturer') }} <span class="text-rose-500">*</span></label>
            <select id="unit-manufacturer-select" class="js-select2 form-select">
                <option value="">{{ __('app.select') }}</option>
                @foreach($manufacturers as $manufacturer)
                    <option value="{{ $manufacturer->id }}" @selected($selectedManufacturerId == $manufacturer->id)>{{ $manufacturer->localized_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">{{ __('equipment.model') }} <span class="text-rose-500">*</span></label>
            <select name="equipment_model_id" id="unit-model-select" class="js-select2 form-select @error('equipment_model_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($models as $m)
                    <option value="{{ $m->id }}"
                            data-manufacturer="{{ $m->equipment_manufacturer_id }}"
                            data-category="{{ $m->equipment_category_id }}"
                            data-subtype="{{ $m->equipment_subtype_id }}"
                            @selected(old('equipment_model_id', $unit?->equipment_model_id) == $m->id)>{{ $m->model_number }}</option>
                @endforeach
            </select>
            @error('equipment_model_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.category') }} <span class="text-rose-500">*</span></label>
            <select name="equipment_category_id" id="unit-category-select" class="js-select2 form-select @error('equipment_category_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('equipment_category_id', $unit?->equipment_category_id) == $category->id)>{{ $category->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_category_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.subtype') }}</label>
            <select name="equipment_subtype_id" id="unit-subtype-select" class="js-select2 form-select @error('equipment_subtype_id') is-invalid @enderror">
                <option value="">{{ __('app.none') }}</option>
                @foreach($subtypes as $subtype)
                    <option value="{{ $subtype->id }}" data-category="{{ $subtype->equipment_category_id }}"
                            @selected(old('equipment_subtype_id', $unit?->equipment_subtype_id) == $subtype->id)>{{ $subtype->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_subtype_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.unit_serial_number') }}</label>
            <input type="text" name="serial_number" value="{{ old('serial_number', $unit?->serial_number) }}" dir="ltr"
                   class="form-input @error('serial_number') is-invalid @enderror">
            @error('serial_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.unit_location') }}</label>
            <input type="text" name="location" value="{{ old('location', $unit?->location) }}"
                   class="form-input @error('location') is-invalid @enderror">
            @error('location')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.unit_project') }}</label>
            <input type="text" name="project" value="{{ old('project', $unit?->project) }}" placeholder="{{ __('equipment.unit_project_hint') }}"
                   class="form-input @error('project') is-invalid @enderror">
            @error('project')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.unit_status') }} <span class="text-rose-500">*</span></label>
            <select name="status" class="js-select2 form-select @error('status') is-invalid @enderror">
                @foreach(\App\Models\EquipmentUnit::STATUSES as $statusOption)
                    <option value="{{ $statusOption }}" @selected(old('status', $unit?->status ?? 'operational') == $statusOption)>{{ __('equipment.unit_status_'.$statusOption) }}</option>
                @endforeach
            </select>
            @error('status')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.unit_report_template') }}</label>
            <select name="report_template_id" class="js-select2 form-select @error('report_template_id') is-invalid @enderror">
                <option value="">{{ __('equipment.unit_report_template_auto') }}</option>
                @foreach($reportTemplates as $rt)
                    <option value="{{ $rt->id }}" @selected(old('report_template_id', $unit?->report_template_id) == $rt->id)>{{ $rt->localized_name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-400 mt-1">{{ __('equipment.unit_report_template_hint') }}</p>
            @error('report_template_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('equipment.superheat_band_override') }}</label>
            <div class="flex items-center gap-3">
                <input type="number" step="0.1" name="superheat_low" value="{{ old('superheat_low', $unit?->reading_limits['superheat']['low'] ?? '') }}" placeholder="{{ __('equipment.superheat_low') }}" class="form-input @error('superheat_low') is-invalid @enderror">
                <span class="text-slate-400">—</span>
                <input type="number" step="0.1" name="superheat_high" value="{{ old('superheat_high', $unit?->reading_limits['superheat']['high'] ?? '') }}" placeholder="{{ __('equipment.superheat_high') }}" class="form-input @error('superheat_high') is-invalid @enderror">
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ __('equipment.superheat_band_hint') }}</p>
            @error('superheat_low')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @error('superheat_high')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('equipment.unit_notes') }}</label>
            <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $unit?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const manufacturerSelect = document.querySelector('#unit-manufacturer-select');
        const modelSelect = document.querySelector('#unit-model-select');
        const categorySelect = document.querySelector('#unit-category-select');
        const subtypeSelect = document.querySelector('#unit-subtype-select');
        if (!manufacturerSelect || !modelSelect) return;

        function reinit(select) {
            if (!select || !window.jQuery) return;
            jQuery(select).select2('destroy');
            window.initSelect2(select.closest('form'));
        }

        manufacturerSelect.addEventListener('change', function () {
            const manufacturerId = this.value;
            modelSelect.querySelectorAll('option[data-manufacturer]').forEach(function (opt) {
                opt.hidden = manufacturerId !== '' && opt.dataset.manufacturer !== manufacturerId;
            });
            if (modelSelect.value && modelSelect.selectedOptions[0]?.hidden) {
                modelSelect.value = '';
            }
            reinit(modelSelect);
        });

        if (categorySelect && subtypeSelect) {
            categorySelect.addEventListener('change', function () {
                const categoryId = this.value;
                subtypeSelect.querySelectorAll('option[data-category]').forEach(function (opt) {
                    opt.hidden = categoryId !== '' && opt.dataset.category !== categoryId;
                });
                if (subtypeSelect.value && subtypeSelect.selectedOptions[0]?.hidden) {
                    subtypeSelect.value = '';
                }
                reinit(subtypeSelect);
            });
        }

        // Picking a model fills in its category/subtype automatically — still editable afterward.
        modelSelect.addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            if (!opt || !opt.value) return;

            if (categorySelect && opt.dataset.category) {
                categorySelect.value = opt.dataset.category;
                reinit(categorySelect);
                categorySelect.dispatchEvent(new Event('change'));
            }
            if (subtypeSelect) {
                subtypeSelect.value = opt.dataset.subtype || '';
                reinit(subtypeSelect);
            }
        });
    });
</script>
@endpush
