@php $model = $model ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-microchip text-cyan-500 text-sm"></i>
            {{ __('equipment.model') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('equipment.manufacturer') }} <span class="text-rose-500">*</span></label>
            <select name="equipment_manufacturer_id" class="js-select2 form-select @error('equipment_manufacturer_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($manufacturers as $manufacturer)
                    <option value="{{ $manufacturer->id }}" @selected(old('equipment_manufacturer_id', $model?->equipment_manufacturer_id) == $manufacturer->id)>{{ $manufacturer->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_manufacturer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.model_number') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="model_number" value="{{ old('model_number', $model?->model_number) }}" dir="ltr"
                   class="form-input @error('model_number') is-invalid @enderror">
            @error('model_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.category') }} <span class="text-rose-500">*</span></label>
            <select name="equipment_category_id" id="model-category-select" class="js-select2 form-select @error('equipment_category_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('equipment_category_id', $model?->equipment_category_id) == $category->id)>{{ $category->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_category_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.subtype') }}</label>
            <select name="equipment_subtype_id" id="model-subtype-select" class="js-select2 form-select @error('equipment_subtype_id') is-invalid @enderror">
                <option value="">{{ __('app.none') }}</option>
                @foreach($subtypes as $subtype)
                    <option value="{{ $subtype->id }}" data-category="{{ $subtype->equipment_category_id }}"
                            @selected(old('equipment_subtype_id', $model?->equipment_subtype_id) == $subtype->id)>{{ $subtype->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_subtype_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.control_system') }}</label>
            <select name="control_system_id" class="js-select2 form-select @error('control_system_id') is-invalid @enderror">
                <option value="">{{ __('app.none') }}</option>
                @foreach($controlSystems as $cs)
                    <option value="{{ $cs->id }}" @selected(old('control_system_id', $model?->control_system_id) == $cs->id)>{{ $cs->localized_name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-400 mt-1">{{ __('equipment.control_system_hint') }}</p>
            @error('control_system_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('equipment.superheat_band_override') }}</label>
            <div class="flex items-center gap-3">
                <input type="number" step="0.1" name="superheat_low" value="{{ old('superheat_low', $model?->reading_limits['superheat']['low'] ?? '') }}" placeholder="{{ __('equipment.superheat_low') }}" class="form-input @error('superheat_low') is-invalid @enderror">
                <span class="text-slate-400">—</span>
                <input type="number" step="0.1" name="superheat_high" value="{{ old('superheat_high', $model?->reading_limits['superheat']['high'] ?? '') }}" placeholder="{{ __('equipment.superheat_high') }}" class="form-input @error('superheat_high') is-invalid @enderror">
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ __('equipment.superheat_band_hint') }}</p>
            @error('superheat_low')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @error('superheat_high')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        @if($model)
        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer" @checked(old('status', $model->status ?? true))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
            </label>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.querySelector('#model-category-select');
        const subtypeSelect = document.querySelector('#model-subtype-select');
        if (!categorySelect || !subtypeSelect) return;

        categorySelect.addEventListener('change', function () {
            const categoryId = this.value;
            subtypeSelect.querySelectorAll('option[data-category]').forEach(function (opt) {
                opt.hidden = categoryId !== '' && opt.dataset.category !== categoryId;
            });
            if (subtypeSelect.value && subtypeSelect.selectedOptions[0]?.hidden) {
                subtypeSelect.value = '';
            }
            if (window.jQuery) {
                jQuery(subtypeSelect).select2('destroy');
                window.initSelect2(subtypeSelect.closest('form'));
            }
        });
    });
</script>
@endpush
