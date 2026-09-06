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
