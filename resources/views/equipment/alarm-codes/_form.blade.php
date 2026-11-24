@php $alarm = $alarm ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-cyan-500 text-sm"></i>
            {{ __('equipment.alarm_code') }}
        </h3>
        <p class="text-xs text-slate-400 mt-1">{{ __('equipment.alarm_code_hint') }}</p>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('equipment.control_system') }} <span class="text-rose-500">*</span></label>
            <select name="control_system_id" class="js-select2 form-select @error('control_system_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($controlSystems as $cs)
                    <option value="{{ $cs->id }}" @selected(old('control_system_id', $alarm?->control_system_id) == $cs->id)>{{ $cs->localized_name }}</option>
                @endforeach
            </select>
            @error('control_system_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.alarm_code_value') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $alarm?->code) }}" dir="ltr" placeholder="D4.1"
                   class="form-input @error('code') is-invalid @enderror">
            @error('code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.alarm_name') }} ({{ __('equipment.doc_lang_en') }}) <span class="text-rose-500">*</span></label>
            <input type="text" name="name_en" value="{{ old('name_en', $alarm?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.alarm_name') }} ({{ __('equipment.doc_lang_ar') }})</label>
            <input type="text" name="name_ar" value="{{ old('name_ar', $alarm?->name_ar) }}"
                   class="form-input @error('name_ar') is-invalid @enderror">
            @error('name_ar')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('equipment.alarm_section') }}</label>
            <input type="text" name="section" value="{{ old('section', $alarm?->section) }}" class="form-input">
        </div>

        @foreach([
            ['causes', 'alarm_causes'], ['checks', 'alarm_checks'], ['effect', 'alarm_effect'],
            ['action', 'alarm_action'], ['reset', 'alarm_reset'],
        ] as [$field, $labelKey])
        <div>
            <label class="form-label">{{ __('equipment.'.$labelKey) }} ({{ __('equipment.doc_lang_en') }})</label>
            <textarea name="{{ $field }}_en" rows="3" dir="ltr" class="form-input">{{ old($field.'_en', $alarm?->{$field.'_en'}) }}</textarea>
        </div>
        <div>
            <label class="form-label">{{ __('equipment.'.$labelKey) }} ({{ __('equipment.doc_lang_ar') }})</label>
            <textarea name="{{ $field }}_ar" rows="3" class="form-input">{{ old($field.'_ar', $alarm?->{$field.'_ar'}) }}</textarea>
        </div>
        @endforeach

        <div>
            <label class="form-label">{{ __('equipment.alarm_source_document') }} <span class="text-rose-500">*</span></label>
            <select name="source_document_id" class="js-select2 form-select @error('source_document_id') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach($documents as $document)
                    <option value="{{ $document->id }}" @selected(old('source_document_id', $alarm?->source_document_id) == $document->id)>{{ $document->localized_name }}</option>
                @endforeach
            </select>
            @error('source_document_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('equipment.alarm_source_page') }} <span class="text-rose-500">*</span></label>
            <input type="number" name="source_page" min="1" value="{{ old('source_page', $alarm?->source_page) }}" dir="ltr" class="form-input @error('source_page') is-invalid @enderror">
            @error('source_page')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
