@php
    $document = $document ?? null;
    $assignment = $document?->assignment;
@endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-cyan-500 text-sm"></i>
            {{ __('equipment.document') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('equipment.document_type') }} <span class="text-rose-500">*</span></label>
            <select name="document_type_id" class="js-select2 form-select @error('document_type_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected(old('document_type_id', $document?->document_type_id) == $type->id)>{{ $type->localized_name }}</option>
                @endforeach
            </select>
            @error('document_type_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.document_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $document?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $document?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.document_language') }} <span class="text-rose-500">*</span></label>
            <select name="language" class="js-select2 form-select @error('language') is-invalid @enderror">
                @foreach(['ar' => 'equipment.doc_lang_ar', 'en' => 'equipment.doc_lang_en', 'both' => 'equipment.doc_lang_both', 'other' => 'equipment.doc_lang_other'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('language', $document?->language ?? 'ar') == $value)>{{ __($label) }}</option>
                @endforeach
            </select>
            @error('language')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.document_revision') }}</label>
            <input type="text" name="revision" value="{{ old('revision', $document?->revision) }}" dir="ltr"
                   class="form-input @error('revision') is-invalid @enderror">
            @error('revision')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.document_tags') }}</label>
            <input type="text" name="tags" value="{{ old('tags', $document?->tags) }}" placeholder="{{ __('equipment.document_tags_hint') }}"
                   class="form-input @error('tags') is-invalid @enderror">
            @error('tags')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('equipment.document_file') }} @if(!$document)<span class="text-rose-500">*</span>@endif</label>
            <input type="file" name="file" class="form-input @error('file') is-invalid @enderror">
            @if($document)
                <p class="text-xs text-slate-400 mt-1">{{ __('equipment.document_file_replace_hint') }} — <a href="{{ $document->url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $document->original_name }}</a></p>
            @endif
            @error('file')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card mb-5" x-data="{ level: '{{ old('level', $assignment?->level ?? 'all') }}' }" x-init="$nextTick(() => window.initSelect2())">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-diagram-project text-cyan-500 text-sm"></i>
            {{ __('equipment.document_scope') }}
        </h3>
        <p class="text-xs text-slate-400 mt-1">{{ __('equipment.document_scope_hint') }}</p>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('equipment.document_scope_level') }} <span class="text-rose-500">*</span></label>
            <select name="level" x-model="level"
                    @change="$nextTick(() => { $(document).find('.js-scope-select.select2-hidden-accessible').select2('destroy'); window.initSelect2(); })"
                    class="form-select @error('level') is-invalid @enderror">
                <option value="unit" @selected(old('level', $assignment?->level) == 'unit')>{{ __('equipment.doc_scope_level_unit') }}</option>
                <option value="model" @selected(old('level', $assignment?->level) == 'model')>{{ __('equipment.doc_scope_level_model') }}</option>
                <option value="subtype" @selected(old('level', $assignment?->level) == 'subtype')>{{ __('equipment.doc_scope_level_subtype') }}</option>
                <option value="category" @selected(old('level', $assignment?->level) == 'category')>{{ __('equipment.doc_scope_level_category') }}</option>
                <option value="manufacturer" @selected(old('level', $assignment?->level) == 'manufacturer')>{{ __('equipment.doc_scope_level_manufacturer') }}</option>
                <option value="all" @selected(old('level', $assignment?->level ?? 'all') == 'all')>{{ __('equipment.doc_scope_level_all') }}</option>
            </select>
            @error('level')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="level === 'unit'">
            <label class="form-label">{{ __('equipment.unit') }}</label>
            <select name="equipment_unit_id" class="js-select2 js-scope-select form-select @error('equipment_unit_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected(old('equipment_unit_id', $assignment?->equipment_unit_id) == $unit->id)>{{ $unit->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_unit_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="level === 'model'">
            <label class="form-label">{{ __('equipment.model') }}</label>
            <select name="equipment_model_id" class="js-select2 js-scope-select form-select @error('equipment_model_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($models as $m)
                    <option value="{{ $m->id }}" @selected(old('equipment_model_id', $assignment?->equipment_model_id) == $m->id)>{{ $m->model_number }}</option>
                @endforeach
            </select>
            @error('equipment_model_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="level === 'subtype'">
            <label class="form-label">{{ __('equipment.subtype') }}</label>
            <select name="equipment_subtype_id" class="js-select2 js-scope-select form-select @error('equipment_subtype_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($subtypes as $subtype)
                    <option value="{{ $subtype->id }}" @selected(old('equipment_subtype_id', $assignment?->equipment_subtype_id) == $subtype->id)>{{ $subtype->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_subtype_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="level === 'category'">
            <label class="form-label">{{ __('equipment.category') }}</label>
            <select name="equipment_category_id" class="js-select2 js-scope-select form-select @error('equipment_category_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('equipment_category_id', $assignment?->equipment_category_id) == $category->id)>{{ $category->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_category_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="level === 'manufacturer'">
            <label class="form-label">{{ __('equipment.manufacturer') }}</label>
            <select name="equipment_manufacturer_id" class="js-select2 js-scope-select form-select @error('equipment_manufacturer_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($manufacturers as $manufacturer)
                    <option value="{{ $manufacturer->id }}" @selected(old('equipment_manufacturer_id', $assignment?->equipment_manufacturer_id) == $manufacturer->id)>{{ $manufacturer->localized_name }}</option>
                @endforeach
            </select>
            @error('equipment_manufacturer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-show="level === 'all'" class="flex items-center">
            <p class="text-sm text-slate-500">{{ __('equipment.doc_scope_all_hint') }}</p>
        </div>
    </div>
</div>
