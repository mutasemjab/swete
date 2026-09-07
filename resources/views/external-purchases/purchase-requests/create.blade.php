@extends('layouts.app')

@section('title', __('external_purchases.add_purchase_request'))
@section('breadcrumb', __('external_purchases.add_purchase_request'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.add_purchase_request') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.add_purchase_request_subtitle') }}</p>
    </div>
    <a href="{{ $project ? route('projects.show', $project) : route('purchase-requests.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('purchase-requests.store') }}" method="POST"
      x-data="{
        linkType: '{{ old('link_type', $project ? 'project' : ($serviceCall ? 'service_call' : 'stock')) }}',
        scope: '{{ old('location_scope', 'inside_jordan') }}',
        branches: {{ $branches->map(fn ($b) => ['id' => $b->id, 'addressLines' => $b->localized_address_lines])->values()->toJson() }},
        branchId: '{{ old('branch_id') }}',
        get branchAddressLines() {
            const b = this.branches.find(x => String(x.id) === String(this.branchId));
            return b ? b.addressLines : [];
        },
        items: [{ material_id: '', quantity: '', unit_price: '' }],
        addItem() { this.items.push({ material_id: '', quantity: '', unit_price: '' }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">
    @csrf

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-cyan-500 text-sm"></i>
                {{ __('external_purchases.purchase_request') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('external_purchases.request_link_type') }}</label>
                <select x-model="linkType" class="form-select">
                    <option value="project">{{ __('external_purchases.link_type_project') }}</option>
                    <option value="service_call">{{ __('external_purchases.link_type_service_call') }}</option>
                    <option value="stock">{{ __('external_purchases.link_type_stock') }}</option>
                </select>
            </div>

            <div x-show="linkType === 'project'">
                <label class="form-label">{{ __('external_purchases.request_project') }}</label>
                <select name="project_id" class="js-select2 form-select @error('project_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(old('project_id', $project?->id) == $p->id)>{{ $p->number }} — {{ $p->localized_title }}</option>
                    @endforeach
                </select>
                @error('project_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div x-show="linkType === 'service_call'">
                <label class="form-label">{{ __('external_purchases.request_service_call') }}</label>
                <select name="service_call_id" class="js-select2 form-select @error('service_call_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($serviceCalls as $sc)
                        <option value="{{ $sc->id }}" @selected(old('service_call_id', $serviceCall?->id) == $sc->id)>{{ $sc->number }} — {{ $sc->title }}</option>
                    @endforeach
                </select>
                @error('service_call_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_supplier') }} <span class="text-rose-500">*</span></label>
                <select name="supplier_id" class="js-select2 form-select @error('supplier_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->localized_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_currency') }}</label>
                <select name="currency_id" class="js-select2 form-select @error('currency_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->localized_name }} ({{ $currency->code }})</option>
                    @endforeach
                </select>
                @error('currency_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_branch') }} <span class="text-rose-500">*</span></label>
                <select name="branch_id" x-model="branchId" class="js-select2 form-select @error('branch_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->localized_name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.request_address') }}</label>
                <div class="form-input bg-slate-50 text-slate-600 h-auto py-2.5 leading-6">
                    <template x-for="line in branchAddressLines" :key="line">
                        <p x-text="line"></p>
                    </template>
                    <p x-show="branchAddressLines.length === 0">—</p>
                </div>
            </div>

            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">{{ __('external_purchases.request_shipping_address') }}</label>
                </div>

                <div>
                    <label class="form-label">{{ __('app.address_line1') }}</label>
                    <input type="text" name="shipping_address_line1" value="{{ old('shipping_address_line1') }}"
                           class="form-input @error('shipping_address_line1') is-invalid @enderror">
                    @error('shipping_address_line1')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.address_line1_en') }}</label>
                    <input type="text" name="shipping_address_line1_en" value="{{ old('shipping_address_line1_en') }}" dir="ltr"
                           class="form-input @error('shipping_address_line1_en') is-invalid @enderror">
                    @error('shipping_address_line1_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.po_box') }}</label>
                    <input type="text" name="shipping_po_box" value="{{ old('shipping_po_box') }}" dir="ltr"
                           class="form-input @error('shipping_po_box') is-invalid @enderror">
                    @error('shipping_po_box')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.postal_code') }}</label>
                    <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" dir="ltr"
                           class="form-input @error('shipping_postal_code') is-invalid @enderror">
                    @error('shipping_postal_code')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.city') }}</label>
                    <input type="text" name="shipping_city" value="{{ old('shipping_city') }}"
                           class="form-input @error('shipping_city') is-invalid @enderror">
                    @error('shipping_city')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.city_en') }}</label>
                    <input type="text" name="shipping_city_en" value="{{ old('shipping_city_en') }}" dir="ltr"
                           class="form-input @error('shipping_city_en') is-invalid @enderror">
                    @error('shipping_city_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.country') }}</label>
                    <input type="text" name="shipping_country" value="{{ old('shipping_country') }}"
                           class="form-input @error('shipping_country') is-invalid @enderror">
                    @error('shipping_country')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">{{ __('app.country_en') }}</label>
                    <input type="text" name="shipping_country_en" value="{{ old('shipping_country_en') }}" dir="ltr"
                           class="form-input @error('shipping_country_en') is-invalid @enderror">
                    @error('shipping_country_en')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
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
                            <option value="{{ $key }}" @selected(old('governorate') === $key)>{{ $names[app()->getLocale() === 'en' ? 'en' : 'ar'] }}</option>
                        @endforeach
                    </select>
                    @error('governorate')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>

                <div x-show="scope === 'outside_jordan'">
                    <label class="form-label">{{ __('tenders.tender_country') }}</label>
                    <select name="country_id" class="js-select2 form-select @error('country_id') is-invalid @enderror">
                        <option value="">{{ __('app.select') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>{{ $country->localized_name }}</option>
                        @endforeach
                    </select>
                    @error('country_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('external_purchases.request_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-cyan-500 text-sm"></i>
                {{ __('external_purchases.request_items') }}
            </h3>
            <button type="button" @click="addItem()" class="btn-secondary btn-sm">
                <i class="fa-solid fa-plus"></i>
                {{ __('accounting.invoice_add_item') }}
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('warehouse.material') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('warehouse.voucher_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-36">{{ __('accounting.invoice_item_unit_price') }}</th>
                        <th class="px-5 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-5 py-2.5">
                                <select :name="`items[${index}][material_id]`" x-model="item.material_id" class="js-select2 form-select" required>
                                    <option value="">{{ __('app.select') }}</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">{{ $material->localized_name }} ({{ $material->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       step="0.001" min="0.001" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5">
                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                       step="0.001" min="0" dir="ltr" class="form-input" required>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                <button type="button" @click="removeItem(index)" title="{{ __('accounting.invoice_remove_item') }}"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        @error('items')<p class="form-error px-5 py-3"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('external_purchases.add_purchase_request') }}
        </button>
        <a href="{{ $project ? route('projects.show', $project) : route('purchase-requests.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
