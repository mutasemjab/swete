@php $tender = $tender ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-gavel text-orange-500 text-sm"></i>
            {{ __('tenders.tender') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('tenders.tender_title') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $tender?->title) }}"
                   class="form-input @error('title') is-invalid @enderror">
            @error('title')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="title_en" value="{{ old('title_en', $tender?->title_en) }}" dir="ltr"
                   class="form-input @error('title_en') is-invalid @enderror">
            @error('title_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_entity_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="entity_name" value="{{ old('entity_name', $tender?->entity_name) }}"
                   class="form-input @error('entity_name') is-invalid @enderror">
            @error('entity_name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="entity_name_en" value="{{ old('entity_name_en', $tender?->entity_name_en) }}" dir="ltr"
                   class="form-input @error('entity_name_en') is-invalid @enderror">
            @error('entity_name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_submission_deadline') }} <span class="text-rose-500">*</span></label>
            <input type="date" name="submission_deadline" value="{{ old('submission_deadline', $tender?->submission_deadline?->toDateString()) }}"
                   class="form-input @error('submission_deadline') is-invalid @enderror">
            @error('submission_deadline')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_status') }}</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach(['open','closed','won','lost'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $tender?->status ?? 'open') === $status)>{{ __('tenders.tender_status_' . $status) }}</option>
                @endforeach
            </select>
            @error('status')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.tender_notes') }}</label>
            <textarea name="notes" rows="3" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $tender?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
