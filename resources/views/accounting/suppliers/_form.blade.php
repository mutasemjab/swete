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
                <select name="governorate" class="js-select2 form-select @error('governorate') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach(\App\Models\Tender::JORDAN_GOVERNORATES as $key => $names)
                        <option value="{{ $key }}" @selected(old('governorate', $supplier?->governorate) === $key)>{{ $names[app()->getLocale() === 'en' ? 'en' : 'ar'] }}</option>
                    @endforeach
                </select>
                @error('governorate')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
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
            <i class="fa-solid fa-truck-ramp-box text-blue-500 text-sm"></i>
            {{ __('accounting.supplier_default_shipping_address') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <p class="text-xs text-slate-400 sm:col-span-2">{{ __('accounting.supplier_default_shipping_address_hint') }}</p>

        <div>
            <label class="form-label">{{ __('app.address_line1') }}</label>
            <input type="text" name="shipping_address_line1" value="{{ old('shipping_address_line1', $supplier?->shipping_address_line1) }}"
                   class="form-input @error('shipping_address_line1') is-invalid @enderror">
            @error('shipping_address_line1')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.address_line1_en') }}</label>
            <input type="text" name="shipping_address_line1_en" value="{{ old('shipping_address_line1_en', $supplier?->shipping_address_line1_en) }}" dir="ltr"
                   class="form-input @error('shipping_address_line1_en') is-invalid @enderror">
            @error('shipping_address_line1_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.po_box') }}</label>
            <input type="text" name="shipping_po_box" value="{{ old('shipping_po_box', $supplier?->shipping_po_box) }}" dir="ltr"
                   class="form-input @error('shipping_po_box') is-invalid @enderror">
            @error('shipping_po_box')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.postal_code') }}</label>
            <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code', $supplier?->shipping_postal_code) }}" dir="ltr"
                   class="form-input @error('shipping_postal_code') is-invalid @enderror">
            @error('shipping_postal_code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.city') }}</label>
            <input type="text" name="shipping_city" value="{{ old('shipping_city', $supplier?->shipping_city) }}"
                   class="form-input @error('shipping_city') is-invalid @enderror">
            @error('shipping_city')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.city_en') }}</label>
            <input type="text" name="shipping_city_en" value="{{ old('shipping_city_en', $supplier?->shipping_city_en) }}" dir="ltr"
                   class="form-input @error('shipping_city_en') is-invalid @enderror">
            @error('shipping_city_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.country') }}</label>
            <input type="text" name="shipping_country" value="{{ old('shipping_country', $supplier?->shipping_country) }}"
                   class="form-input @error('shipping_country') is-invalid @enderror">
            @error('shipping_country')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.country_en') }}</label>
            <input type="text" name="shipping_country_en" value="{{ old('shipping_country_en', $supplier?->shipping_country_en) }}" dir="ltr"
                   class="form-input @error('shipping_country_en') is-invalid @enderror">
            @error('shipping_country_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-blue-500 text-sm"></i>
            {{ __('accounting.supplier_shipping_instruction') }}
        </h3>
    </div>
    <div class="px-6 py-5">
        <textarea name="shipping_instruction" rows="6" class="form-input @error('shipping_instruction') is-invalid @enderror">{{ old('shipping_instruction', $supplier?->shipping_instruction) }}</textarea>
        @error('shipping_instruction')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
    </div>
</div>

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
