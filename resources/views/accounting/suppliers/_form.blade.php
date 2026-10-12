@php $supplier = $supplier ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-truck text-blue-500 text-sm"></i>
            {{ __('accounting.supplier') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('accounting.party_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $supplier?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $supplier?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_group') }}</label>
            <select name="supplier_group_id" class="form-select @error('supplier_group_id') is-invalid @enderror">
                <option value="">{{ __('accounting.no_parent_group') }}</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected(old('supplier_group_id', $supplier?->supplier_group_id) == $group->id)>{{ $group->path }}</option>
                @endforeach
            </select>
            @error('supplier_group_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_phone') }}</label>
            <input type="text" name="phone" value="{{ old('phone', $supplier?->phone) }}" dir="ltr"
                   class="form-input @error('phone') is-invalid @enderror">
            @error('phone')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_email') }}</label>
            <input type="email" name="email" value="{{ old('email', $supplier?->email) }}" dir="ltr"
                   class="form-input @error('email') is-invalid @enderror">
            @error('email')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_tax_number') }}</label>
            <input type="text" name="tax_number" value="{{ old('tax_number', $supplier?->tax_number) }}" dir="ltr"
                   class="form-input @error('tax_number') is-invalid @enderror">
            @error('tax_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('accounting.party_address') }}</label>
            <textarea name="address" rows="2" class="form-input @error('address') is-invalid @enderror">{{ old('address', $supplier?->address) }}</textarea>
            @error('address')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div x-data="{ scope: '{{ old('location_scope', $supplier?->location_scope ?? 'inside_jordan') }}' }" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('accounting.supplier_location_scope') }}</label>
                <select name="location_scope" x-model="scope" class="form-select @error('location_scope') is-invalid @enderror">
                    <option value="inside_jordan">{{ __('tenders.location_inside_jordan') }}</option>
                    <option value="outside_jordan">{{ __('tenders.location_outside_jordan') }}</option>
                </select>
                @error('location_scope')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="scope === 'inside_jordan'">
                <label class="form-label">{{ __('tenders.tender_governorate') }}</label>
                <select name="governorate_id" class="js-select2 form-select @error('governorate_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($governorates as $governorate)
                        <option value="{{ $governorate->id }}" @selected(old('governorate_id', $supplier?->governorate_id) == $governorate->id)>{{ $governorate->localized_name }}</option>
                    @endforeach
                </select>
                @error('governorate_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="scope === 'outside_jordan'">
                <label class="form-label">{{ __('tenders.tender_country') }}</label>
                <select name="country_id" class="js-select2 form-select @error('country_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('country_id', $supplier?->country_id) == $country->id)>{{ $country->localized_name }}</option>
                    @endforeach
                </select>
                @error('country_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_opening_balance') }}</label>
            <input type="number" name="opening_balance" value="{{ old('opening_balance', $supplier?->opening_balance ?? 0) }}"
                   step="0.001" dir="ltr" class="form-input @error('opening_balance') is-invalid @enderror">
            @error('opening_balance')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-building text-violet-500 text-sm"></i>
            {{ __('clients.company_id_details') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('clients.company_name') }}</label>
            <input type="text" name="company_name" value="{{ old('company_name', $supplier?->company_name) }}"
                   class="form-input @error('company_name') is-invalid @enderror">
            @error('company_name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label">{{ __('clients.company_name_en') }}</label>
            <input type="text" name="company_name_en" value="{{ old('company_name_en', $supplier?->company_name_en) }}" dir="ltr"
                   class="form-input @error('company_name_en') is-invalid @enderror">
            @error('company_name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label">{{ __('clients.commercial_registration') }}</label>
            <input type="text" name="commercial_registration" value="{{ old('commercial_registration', $supplier?->commercial_registration) }}"
                   class="form-input @error('commercial_registration') is-invalid @enderror">
            @error('commercial_registration')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label">{{ __('clients.personal_id') }}</label>
            <input type="text" name="personal_id" value="{{ old('personal_id', $supplier?->personal_id) }}"
                   class="form-input @error('personal_id') is-invalid @enderror">
            @error('personal_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card mb-5" x-data="{ contacts: {{ json_encode(old('contacts', $seedContacts)) }} }">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-user-group text-teal-500 text-sm"></i>
            {{ __('clients.contacts_section') }}
        </h3>
        <button type="button" @click="contacts.push({ name: '', phone: '', job_title: '' })"
                class="btn-secondary btn-sm">
            <i class="fa-solid fa-plus"></i>
            {{ __('clients.add_contact') }}
        </button>
    </div>
    <div class="px-6 py-5 space-y-4">
        <template x-for="(contact, index) in contacts" :key="index">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl">
                <div>
                    <label class="form-label">{{ __('clients.contact_name') }}</label>
                    <input type="text" :name="`contacts[${index}][name]`" x-model="contact.name"
                           class="form-input">
                </div>
                <div>
                    <label class="form-label">{{ __('clients.contact_phone') }}</label>
                    <input type="text" :name="`contacts[${index}][phone]`" x-model="contact.phone"
                           class="form-input" dir="ltr">
                </div>
                <div>
                    <label class="form-label">{{ __('clients.contact_job_title') }}</label>
                    <div class="flex gap-2">
                        <input type="text" :name="`contacts[${index}][job_title]`" x-model="contact.job_title"
                               class="form-input flex-1">
                        <button type="button" @click="contacts.splice(index, 1)"
                                class="btn-danger btn-sm self-end flex-shrink-0" title="{{ __('app.delete') }}">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <p x-show="contacts.length === 0" class="text-slate-400 text-sm text-center py-4">{{ __('clients.no_contacts') }}</p>
    </div>
</div>

@if($extraFields->isNotEmpty())
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-sliders text-amber-500 text-sm"></i>
            {{ __('clients.extra_fields_section') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach($extraFields as $field)
            @php $efv = ($extraFieldValues ?? collect())->get($field->id); @endphp
            <div class="{{ in_array($field->field_type, ['textarea', 'image']) ? 'sm:col-span-2' : '' }}">
                <label class="form-label">{{ $field->localized_label }}</label>
                @if($field->field_type === 'textarea')
                    <textarea name="extra_fields[{{ $field->id }}]" rows="3"
                              class="form-input">{{ old("extra_fields.{$field->id}", $efv?->value) }}</textarea>
                @elseif($field->field_type === 'image')
                    @if($efv?->file_path)
                        <div class="mb-2">
                            <img src="{{ asset($efv->file_path) }}" alt="" class="h-24 rounded-lg object-cover border border-slate-200">
                        </div>
                    @endif
                    <input type="file" name="extra_fields_images[{{ $field->id }}]"
                           class="form-input @error("extra_fields_images.{$field->id}") is-invalid @enderror"
                           accept="image/*">
                @elseif($field->field_type === 'number')
                    <input type="number" name="extra_fields[{{ $field->id }}]"
                           value="{{ old("extra_fields.{$field->id}", $efv?->value) }}" dir="ltr"
                           class="form-input">
                @elseif($field->field_type === 'date')
                    <input type="date" name="extra_fields[{{ $field->id }}]"
                           value="{{ old("extra_fields.{$field->id}", $efv?->value) }}" dir="ltr"
                           class="form-input">
                @else
                    <input type="text" name="extra_fields[{{ $field->id }}]"
                           value="{{ old("extra_fields.{$field->id}", $efv?->value) }}"
                           class="form-input">
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-blue-500 text-sm"></i>
            {{ __('accounting.supplier_shipping_instruction') }}
        </h3>
    </div>
    <div class="px-6 py-5">
        <textarea id="shipping_instruction" name="shipping_instruction" class="js-richtext form-input @error('shipping_instruction') is-invalid @enderror">{{ old('shipping_instruction', $supplier?->shipping_instruction) }}</textarea>
        @error('shipping_instruction')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        window.initRichText({
            images_upload_handler: function (blobInfo) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    fetch('{{ route('accounting.suppliers.shipping-instruction-image') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: formData,
                    })
                        .then((res) => res.json())
                        .then((data) => resolve(data.location))
                        .catch(() => reject('{{ __('app.error_occurred') }}'));
                });
            },
        });
    </script>
@endpush

<div class="card mb-5 px-6 py-5">
    <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
        <input type="hidden" name="status" value="0">
        <input type="checkbox" name="status" value="1" class="sr-only peer"
               @checked(old('status', $supplier?->status ?? true))>
        <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer
                    peer-checked:bg-indigo-600 transition-all
                    after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                    after:bg-white after:rounded-full after:h-5 after:w-5
                    after:transition-all peer-checked:after:translate-x-full"></div>
        <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('app.active') }}</span>
    </label>
</div>
