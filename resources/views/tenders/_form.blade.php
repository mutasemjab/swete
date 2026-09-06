@php
    $tender = $tender ?? null;
    $isService = $type === 'service_call';
@endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid {{ $isService ? 'fa-headset' : 'fa-gavel' }} text-orange-500 text-sm"></i>
            {{ __('tenders.type_' . $type) }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">
                {{ $isService ? __('tenders.service_title') : __('tenders.tender_title') }}
                <span class="text-rose-500">*</span>
            </label>
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
            <label class="form-label">
                {{ $isService ? __('tenders.service_entity_name') : __('tenders.tender_entity_name') }}
                <span class="text-rose-500">*</span>
            </label>
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

        <div class="sm:col-span-2">
            <label class="form-label">
                {{ $isService ? __('tenders.service_customer') : __('tenders.tender_customer') }}
            </label>
            <div class="flex items-start gap-2">
                <div class="flex-1">
                    <select id="tender_party_id" name="party_id" class="js-select2 form-select @error('party_id') is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('party_id', $tender?->party_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                        @endforeach
                    </select>
                    @error('party_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
                @include('components.party-quick-add-modal', [
                    'targetSelectId' => 'tender_party_id',
                    'storeRoute'     => route('accounting.customers.store'),
                    'label'          => __('accounting.add_customer'),
                ])
            </div>
        </div>

        <div x-data="{ scope: '{{ old('location_scope', $tender?->location_scope ?? 'inside_jordan') }}' }" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            @if($type === 'tender')
            <div>
                <label class="form-label">{{ __('tenders.tender_location_scope') }} <span class="text-rose-500">*</span></label>
                <select name="location_scope" x-model="scope" class="form-select @error('location_scope') is-invalid @enderror">
                    <option value="inside_jordan">{{ __('tenders.location_inside_jordan') }}</option>
                    <option value="outside_jordan">{{ __('tenders.location_outside_jordan') }}</option>
                </select>
                @error('location_scope')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
            @endif

            <div x-show="scope === 'inside_jordan'">
                <label class="form-label">
                    {{ $isService ? __('tenders.service_governorate') : __('tenders.tender_governorate') }}
                    <span class="text-rose-500">*</span>
                </label>
                <select name="governorate" class="js-select2 form-select @error('governorate') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach(\App\Models\Tender::JORDAN_GOVERNORATES as $key => $names)
                        <option value="{{ $key }}" @selected(old('governorate', $tender?->governorate) === $key)>{{ $names[app()->getLocale() === 'en' ? 'en' : 'ar'] }}</option>
                    @endforeach
                </select>
                @error('governorate')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_win_probability') }}</label>
            <div class="relative">
                <input type="number" name="win_probability" value="{{ old('win_probability', $tender?->win_probability) }}"
                       min="0" max="100" dir="ltr" class="form-input pe-9 @error('win_probability') is-invalid @enderror">
                <span class="absolute top-1/2 -translate-y-1/2 end-3.5 text-slate-400 text-sm">%</span>
            </div>
            @error('win_probability')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">
                {{ $isService ? __('tenders.service_submission_deadline') : __('tenders.tender_submission_deadline') }}
                <span class="text-rose-500">*</span>
            </label>
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
            <label class="form-label">
                {{ $isService ? __('tenders.service_description') : __('tenders.tender_description') }}
            </label>
            <textarea name="description" rows="3" class="form-input @error('description') is-invalid @enderror">{{ old('description', $tender?->description) }}</textarea>
            @error('description')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.tender_notes') }}</label>
            <textarea name="notes" rows="3" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $tender?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>