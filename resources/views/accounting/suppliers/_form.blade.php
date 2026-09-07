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
                <label class="form-label">{{ __('tenders.tender_location_scope') }}</label>
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

        <div class="flex items-center">
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
    </div>
</div>
