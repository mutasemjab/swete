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

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.tender_customer') }}</label>
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
            <div>
                <label class="form-label">{{ __('tenders.tender_location_scope') }} <span class="text-rose-500">*</span></label>
                <select name="location_scope" x-model="scope" class="form-select @error('location_scope') is-invalid @enderror">
                    <option value="inside_jordan">{{ __('tenders.location_inside_jordan') }}</option>
                    <option value="outside_jordan">{{ __('tenders.location_outside_jordan') }}</option>
                </select>
                @error('location_scope')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="scope === 'inside_jordan'">
                <label class="form-label">{{ __('tenders.tender_governorate') }} <span class="text-rose-500">*</span></label>
                <select name="governorate" class="js-select2 form-select @error('governorate') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach(\App\Models\Tender::JORDAN_GOVERNORATES as $key => $names)
                        <option value="{{ $key }}" @selected(old('governorate', $tender?->governorate) === $key)>{{ $names[app()->getLocale() === 'en' ? 'en' : 'ar'] }}</option>
                    @endforeach
                </select>
                @error('governorate')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="scope === 'outside_jordan'">
                <label class="form-label">{{ __('tenders.tender_country') }} <span class="text-rose-500">*</span></label>
                <select name="country_id" class="js-select2 form-select @error('country_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('country_id', $tender?->country_id) == $country->id)>{{ $country->localized_name }}</option>
                    @endforeach
                </select>
                @error('country_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_delivery_terms') }}</label>
            <select name="delivery_terms" class="form-select @error('delivery_terms') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach(\App\Models\Tender::DELIVERY_TERMS as $term)
                    <option value="{{ $term }}" @selected(old('delivery_terms', $tender?->delivery_terms) === $term)>{{ __('tenders.delivery_terms_' . $term) }}</option>
                @endforeach
            </select>
            @error('delivery_terms')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_coverage') }}</label>
            <select name="coverage" class="form-select @error('coverage') is-invalid @enderror">
                <option value="">{{ __('app.select') }}</option>
                @foreach(\App\Models\Tender::COVERAGE_OPTIONS as $option)
                    <option value="{{ $option }}" @selected(old('coverage', $tender?->coverage) === $option)>{{ __('tenders.coverage_' . $option) }}</option>
                @endforeach
            </select>
            @error('coverage')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2 flex flex-wrap items-center gap-6 py-1">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="tax_exempt" value="0">
                <input type="checkbox" name="tax_exempt" value="1" class="sr-only peer" @checked(old('tax_exempt', $tender?->tax_exempt))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('tenders.tender_tax_exempt') }}</span>
            </label>

            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="customs_exempt" value="0">
                <input type="checkbox" name="customs_exempt" value="1" class="sr-only peer" @checked(old('customs_exempt', $tender?->customs_exempt))>
                <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                            peer-checked:bg-indigo-600 transition-all
                            after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                            after:bg-white after:rounded-full after:h-5 after:w-5
                            after:transition-all peer-checked:after:translate-x-full"></div>
                <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('tenders.tender_customs_exempt') }}</span>
            </label>
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
            <label class="form-label">{{ __('tenders.tender_submission_deadline') }} <span class="text-rose-500">*</span></label>
            <input type="date" name="submission_deadline" value="{{ old('submission_deadline', $tender?->submission_deadline?->toDateString()) }}"
                   class="form-input @error('submission_deadline') is-invalid @enderror">
            @error('submission_deadline')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_status') }} <span class="text-rose-500">*</span></label>
            <select name="status_id" class="form-select @error('status_id') is-invalid @enderror">
                @foreach($statuses as $statusOption)
                    <option value="{{ $statusOption->id }}" @selected(old('status_id', $tender?->status_id) == $statusOption->id)>{{ $statusOption->localized_name }}</option>
                @endforeach
            </select>
            @error('status_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_documents_url') }}</label>
            <input type="text" name="documents_url" value="{{ old('documents_url', $tender?->documents_url) }}" dir="ltr"
                   placeholder="https://..." class="form-input @error('documents_url') is-invalid @enderror">
            @error('documents_url')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('tenders.tender_design_documents_url') }}</label>
            <input type="text" name="design_documents_url" value="{{ old('design_documents_url', $tender?->design_documents_url) }}" dir="ltr"
                   placeholder="https://..." class="form-input @error('design_documents_url') is-invalid @enderror">
            @error('design_documents_url')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('tenders.tender_description') }}</label>
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
