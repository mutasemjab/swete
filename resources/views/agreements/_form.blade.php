@php $agreement = $agreement ?? null; @endphp
<div class="card mb-5" x-data="{ partyType: '{{ old('party_type', $agreement?->party_type ?? 'customer') }}' }">
    <div class="card-header">
        <h3 class="font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-signature text-orange-500 text-sm"></i>
            {{ __('agreements.agreement') }}
        </h3>
    </div>
    <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="form-label">{{ __('agreements.party_type') }} <span class="text-rose-500">*</span></label>
            <select name="party_type" x-model="partyType" class="form-select @error('party_type') is-invalid @enderror">
                <option value="customer">{{ __('agreements.party_type_customer') }}</option>
                <option value="supplier">{{ __('agreements.party_type_supplier') }}</option>
            </select>
            @error('party_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('agreements.party') }} <span class="text-rose-500">*</span></label>
            <template x-if="partyType === 'customer'">
                <select name="party_id" class="js-select2 form-select @error('party_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('party_id', $agreement?->party_type === 'customer' ? $agreement?->party_id : null) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
            </template>
            <template x-if="partyType === 'supplier'">
                <select name="party_id" class="js-select2 form-select @error('party_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('party_id', $agreement?->party_type === 'supplier' ? $agreement?->party_id : null) == $supplier->id)>{{ $supplier->localized_name }} ({{ $supplier->code }})</option>
                    @endforeach
                </select>
            </template>
            @error('party_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="form-label">{{ __('agreements.agreement_file') }}
                @if(! $agreement) <span class="text-rose-500">*</span> @endif
            </label>
            <input type="file" name="file" accept="application/pdf" class="form-input @error('file') is-invalid @enderror">
            @error('file')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            @if($agreement)
                <a href="{{ $agreement->url }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline mt-1.5 inline-block">
                    <i class="fa-solid fa-file-pdf"></i> {{ __('agreements.view_current_file') }}
                </a>
            @endif
        </div>

        <div class="sm:col-span-2">
            <label class="form-label">{{ __('app.notes') }}</label>
            <textarea name="notes" rows="3" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $agreement?->notes) }}</textarea>
            @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>
</div>
