@php
    $shipment = $shipment ?? null;
    $selectedPrIds = $shipment ? $shipment->purchaseRequests->pluck('id') : collect(old('purchase_request_ids', []));
@endphp
<div x-data="{ mode: '{{ old('transport_mode', $shipment?->transport_mode ?? 'sea') }}' }">

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-cyan-500 text-sm"></i>
                {{ __('external_purchases.shipment_purchase_requests') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            @if($eligiblePurchaseRequests->isEmpty())
                <p class="text-sm text-slate-400">{{ __('external_purchases.shipment_no_eligible_requests') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($eligiblePurchaseRequests as $pr)
                        <label class="flex items-start gap-3 border border-slate-200 rounded-xl px-4 py-3 cursor-pointer hover:border-indigo-300 transition-colors">
                            <input type="checkbox" name="purchase_request_ids[]" value="{{ $pr->id }}"
                                   class="mt-1" @checked($selectedPrIds->contains($pr->id))>
                            <span>
                                <span class="block font-bold text-slate-800">{{ $pr->number }}</span>
                                <span class="block text-xs text-slate-400">{{ $pr->supplier?->localized_name }} — {{ $pr->date->format('Y-m-d') }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
            @error('purchase_request_ids')<p class="form-error mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-ship text-cyan-500 text-sm"></i>
                {{ __('external_purchases.shipment') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_shipping_company') }} <span class="text-rose-500">*</span></label>
                <select name="shipping_company_id" class="js-select2 form-select @error('shipping_company_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($shippingCompanies as $company)
                        <option value="{{ $company->id }}" @selected(old('shipping_company_id', $shipment?->shipping_company_id) == $company->id)>{{ $company->localized_name }}</option>
                    @endforeach
                </select>
                @error('shipping_company_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_transport_mode') }} <span class="text-rose-500">*</span></label>
                <select name="transport_mode" x-model="mode" class="form-select @error('transport_mode') is-invalid @enderror">
                    <option value="sea">{{ __('external_purchases.transport_mode_sea') }}</option>
                    <option value="land">{{ __('external_purchases.transport_mode_land') }}</option>
                    <option value="air">{{ __('external_purchases.transport_mode_air') }}</option>
                </select>
                @error('transport_mode')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="mode === 'sea'">
                <label class="form-label">{{ __('external_purchases.sea_service_type') }}</label>
                <select name="sea_service_type" class="form-select @error('sea_service_type') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach(\App\Models\Shipment::SEA_SERVICE_TYPES as $type)
                        <option value="{{ $type }}" @selected(old('sea_service_type', $shipment?->sea_service_type) === $type)>{{ __('external_purchases.sea_service_' . $type) }}</option>
                    @endforeach
                </select>
                @error('sea_service_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="mode === 'air'">
                <label class="form-label">{{ __('external_purchases.air_service_type') }}</label>
                <select name="air_service_type" class="form-select @error('air_service_type') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach(\App\Models\Shipment::AIR_SERVICE_TYPES as $type)
                        <option value="{{ $type }}" @selected(old('air_service_type', $shipment?->air_service_type) === $type)>{{ __('external_purchases.air_service_' . $type) }}</option>
                    @endforeach
                </select>
                @error('air_service_type')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_incoterm') }}</label>
                <select name="incoterm" class="js-select2 form-select @error('incoterm') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach(\App\Models\Shipment::INCOTERMS as $term)
                        <option value="{{ $term }}" @selected(old('incoterm', $shipment?->incoterm) === $term)>{{ strtoupper($term) }} — {{ __('external_purchases.incoterm_' . $term) }}</option>
                    @endforeach
                </select>
                @error('incoterm')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_price') }}</label>
                <input type="number" name="price" value="{{ old('price', $shipment?->price) }}"
                       step="0.001" min="0" dir="ltr" class="form-input @error('price') is-invalid @enderror">
                @error('price')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_currency') }}</label>
                <select name="currency_id" class="js-select2 form-select @error('currency_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(old('currency_id', $shipment?->currency_id) == $currency->id)>{{ $currency->localized_name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
                @error('currency_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_shipping_line') }}</label>
                <input type="text" name="shipping_line" value="{{ old('shipping_line', $shipment?->shipping_line) }}" class="form-input @error('shipping_line') is-invalid @enderror">
                @error('shipping_line')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_bol_number') }}</label>
                <input type="text" name="bill_of_lading_number" value="{{ old('bill_of_lading_number', $shipment?->bill_of_lading_number) }}" dir="ltr" class="form-input @error('bill_of_lading_number') is-invalid @enderror">
                @error('bill_of_lading_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.shipment_container_number') }}</label>
                <input type="text" name="container_number" value="{{ old('container_number', $shipment?->container_number) }}" dir="ltr" class="form-input @error('container_number') is-invalid @enderror">
                @error('container_number')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
                    <input type="hidden" name="is_hazardous" value="0">
                    <input type="checkbox" name="is_hazardous" value="1" class="sr-only peer"
                           @checked(old('is_hazardous', $shipment?->is_hazardous))>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-2 peer-focus:ring-rose-400 rounded-full peer
                                peer-checked:bg-rose-600 transition-all
                                after:content-[''] after:absolute after:top-0.5 after:start-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ms-3 text-sm font-semibold text-slate-700">{{ __('external_purchases.shipment_hazardous') }}</span>
                </label>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('external_purchases.shipment_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes', $shipment?->notes) }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
</div>
