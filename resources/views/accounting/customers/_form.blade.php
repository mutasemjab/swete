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
            <label class="form-label">{{ __('accounting.party_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $customer?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
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
