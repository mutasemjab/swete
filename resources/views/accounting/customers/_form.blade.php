@php $customer = $customer ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-address-book text-blue-500 text-sm"></i>
            {{ __('accounting.customer') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('accounting.customer_name_or_company') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $customer?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.customer_name_or_company_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $customer?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_group') }}</label>
            <select name="customer_group_id" class="form-select @error('customer_group_id') is-invalid @enderror">
                <option value="">{{ __('accounting.no_parent_group') }}</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected(old('customer_group_id', $customer?->customer_group_id) == $group->id)>{{ $group->path }}</option>
                @endforeach
            </select>
            @error('customer_group_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_phone') }}</label>
            <input type="text" name="phone" value="{{ old('phone', $customer?->phone) }}" dir="ltr"
                   class="form-input @error('phone') is-invalid @enderror">
            @error('phone')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_email') }}</label>
            <input type="email" name="email" value="{{ old('email', $customer?->email) }}" dir="ltr"
                   class="form-input @error('email') is-invalid @enderror">
            @error('email')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_tax_number') }}</label>
            <input type="text" name="tax_number" value="{{ old('tax_number', $customer?->tax_number) }}" dir="ltr"
                   class="form-input @error('tax_number') is-invalid @enderror">
            @error('tax_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('clients.commercial_registration') }}</label>
            @if($customer?->commercial_registration_url)
                <a href="{{ $customer->commercial_registration_url }}" target="_blank" class="block mb-2">
                    <img src="{{ $customer->commercial_registration_url }}" class="h-20 rounded-lg object-cover border border-slate-200">
                </a>
            @endif
            <input type="file" name="commercial_registration_file" accept="image/*"
                   class="form-input @error('commercial_registration_file') is-invalid @enderror">
            @error('commercial_registration_file')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('clients.personal_id') }}</label>
            @if($customer?->personal_id_url)
                <a href="{{ $customer->personal_id_url }}" target="_blank" class="block mb-2">
                    <img src="{{ $customer->personal_id_url }}" class="h-20 rounded-lg object-cover border border-slate-200">
                </a>
            @endif
            <input type="file" name="personal_id_file" accept="image/*"
                   class="form-input @error('personal_id_file') is-invalid @enderror">
            @error('personal_id_file')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('accounting.party_address') }}</label>
            <textarea name="address" rows="2" class="form-input @error('address') is-invalid @enderror">{{ old('address', $customer?->address) }}</textarea>
            @error('address')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.party_opening_balance') }}</label>
            <input type="number" name="opening_balance" value="{{ old('opening_balance', $customer?->opening_balance ?? 0) }}"
                   step="0.001" dir="ltr" class="form-input @error('opening_balance') is-invalid @enderror">
            @error('opening_balance')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       @checked(old('status', $customer?->status ?? true))>
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

<div class="card mb-5" x-data="{ show: false }">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-lock text-indigo-500 text-sm"></i>
            {{ __('accounting.customer_portal_access') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('accounting.customer_portal_password') }}</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password"
                       class="form-input pe-10 @error('password') is-invalid @enderror"
                       placeholder="{{ __('app.password_min') }}">
                <button type="button" @click="show = !show"
                        class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors">
                    <i :class="show ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid text-sm"></i>
                </button>
            </div>
            @error('password')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            <p class="text-xs text-slate-400 mt-1.5">{{ __('accounting.customer_portal_password_hint') }}</p>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-truck-ramp-box text-blue-500 text-sm"></i>
            {{ __('accounting.customer_default_shipping_address') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <p class="text-xs text-slate-400 sm:col-span-2">{{ __('accounting.customer_default_shipping_address_hint') }}</p>

        <div>
            <label class="form-label">{{ __('app.address_line1') }}</label>
            <input type="text" name="shipping_address_line1" value="{{ old('shipping_address_line1', $customer?->shipping_address_line1) }}"
                   class="form-input @error('shipping_address_line1') is-invalid @enderror">
            @error('shipping_address_line1')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.address_line1_en') }}</label>
            <input type="text" name="shipping_address_line1_en" value="{{ old('shipping_address_line1_en', $customer?->shipping_address_line1_en) }}" dir="ltr"
                   class="form-input @error('shipping_address_line1_en') is-invalid @enderror">
            @error('shipping_address_line1_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.po_box') }}</label>
            <input type="text" name="shipping_po_box" value="{{ old('shipping_po_box', $customer?->shipping_po_box) }}" dir="ltr"
                   class="form-input @error('shipping_po_box') is-invalid @enderror">
            @error('shipping_po_box')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.postal_code') }}</label>
            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $customer?->shipping_postal_code) }}" dir="ltr"
                   class="form-input @error('shipping_postal_code') is-invalid @enderror">
            @error('shipping_postal_code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.city') }}</label>
            <input type="text" name="shipping_city" value="{{ old('shipping_city', $customer?->shipping_city) }}"
                   class="form-input @error('shipping_city') is-invalid @enderror">
            @error('shipping_city')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.city_en') }}</label>
            <input type="text" name="shipping_city_en" value="{{ old('shipping_city_en', $customer?->shipping_city_en) }}" dir="ltr"
                   class="form-input @error('shipping_city_en') is-invalid @enderror">
            @error('shipping_city_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.country') }}</label>
            <input type="text" name="shipping_country" value="{{ old('shipping_country', $customer?->shipping_country) }}"
                   class="form-input @error('shipping_country') is-invalid @enderror">
            @error('shipping_country')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.country_en') }}</label>
            <input type="text" name="shipping_country_en" value="{{ old('shipping_country_en', $customer?->shipping_country_en) }}" dir="ltr"
                   class="form-input @error('shipping_country_en') is-invalid @enderror">
            @error('shipping_country_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
