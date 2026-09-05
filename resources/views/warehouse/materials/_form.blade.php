@php $material = $material ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-cube text-emerald-500 text-sm"></i>
            {{ __('warehouse.material') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('warehouse.material_code') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $material?->code) }}" dir="ltr"
                   class="form-input @error('code') is-invalid @enderror">
            @error('code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('warehouse.material_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $material?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $material?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('warehouse.material_category') }} <span class="text-rose-500">*</span></label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $material?->category_id) == $category->id)>{{ $category->path }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('warehouse.material_unit') }} <span class="text-rose-500">*</span></label>
            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected(old('unit_id', $material?->unit_id) == $unit->id)>{{ $unit->localized_name }}</option>
                @endforeach
            </select>
            @error('unit_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('warehouse.min_stock_level') }}</label>
            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $material?->min_stock_level) }}"
                   step="0.001" min="0" dir="ltr"
                   class="form-input @error('min_stock_level') is-invalid @enderror">
            <p class="text-xs text-slate-400 mt-1.5">{{ __('warehouse.min_stock_level_hint') }}</p>
            @error('min_stock_level')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('warehouse.material_description') }}</label>
            <textarea name="description" rows="3"
                      class="form-input @error('description') is-invalid @enderror">{{ old('description', $material?->description) }}</textarea>
            @error('description')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       @checked(old('status', $material?->status ?? true))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
            </label>
        </div>
    </div>
</div>
