@php $invoiceType = $invoiceType ?? null; @endphp
<div class="card mb-5">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-tags text-blue-500 text-sm"></i>
            {{ __('accounting.invoice_type') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('accounting.invoice_type_name') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $invoiceType?->name) }}"
                   class="form-input @error('name') is-invalid @enderror">
            @error('name')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('app.name_en') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $invoiceType?->name_en) }}" dir="ltr"
                   class="form-input @error('name_en') is-invalid @enderror">
            @error('name_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.invoice_type_code') }} <span class="text-rose-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $invoiceType?->code) }}" dir="ltr"
                   {{ $invoiceType?->is_system ? 'readonly' : '' }}
                   class="form-input @error('code') is-invalid @enderror {{ $invoiceType?->is_system ? 'bg-slate-50 text-slate-400' : '' }}">
            <p class="text-xs text-slate-400 mt-1.5">{{ __('accounting.invoice_type_code_hint') }}</p>
            @error('code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('accounting.invoice_type_party_type') }} <span class="text-rose-500">*</span></label>
            <select name="party_type" class="form-select @error('party_type') is-invalid @enderror" {{ $invoiceType?->is_system ? 'disabled' : '' }}>
                <option value="customer" @selected(old('party_type', $invoiceType?->party_type) === 'customer')>{{ __('accounting.party_type_customer') }}</option>
                <option value="supplier" @selected(old('party_type', $invoiceType?->party_type) === 'supplier')>{{ __('accounting.party_type_supplier') }}</option>
            </select>
            @if($invoiceType?->is_system)
                <input type="hidden" name="party_type" value="{{ $invoiceType->party_type }}">
            @endif
            @error('party_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" class="sr-only peer"
                       @checked(old('status', $invoiceType?->status ?? true))>
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
