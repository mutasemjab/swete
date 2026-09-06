@extends('layouts.app')

@section('title', __('accounting.add_invoice'))
@section('breadcrumb', __('accounting.add_invoice'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_invoice') }}</h1>
        <p class="page-subtitle">{{ __('accounting.add_invoice_subtitle') }}</p>
    </div>
    <a href="{{ route('accounting.invoices.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('accounting.invoices.store') }}" method="POST"
      x-data="{
        invoiceTypeId: '{{ old('invoice_type_id') }}',
        partyTypeByType: {{ Illuminate\Support\Js::from($invoiceTypes->pluck('party_type', 'id')) }},
        get partyType() { return this.partyTypeByType[this.invoiceTypeId] ?? null; },
        items: [{ description: '', quantity: 1, unit_price: '' }],
        addItem() { this.items.push({ description: '', quantity: 1, unit_price: '' }); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
      }">
    @csrf

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-blue-500 text-sm"></i>
                {{ __('accounting.invoice') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="form-label">{{ __('accounting.invoice_type') }} <span class="text-rose-500">*</span></label>
                <select name="invoice_type_id" x-model="invoiceTypeId" class="form-select @error('invoice_type_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($invoiceTypes as $invoiceType)
                        <option value="{{ $invoiceType->id }}">{{ $invoiceType->localized_name }}</option>
                    @endforeach
                </select>
                @error('invoice_type_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('accounting.invoice_party') }} <span class="text-rose-500">*</span></label>

                <select name="party_id" x-show="partyType === 'customer'" :disabled="partyType !== 'customer'"
                        class="form-select @error('party_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('party_id') == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>

                <select name="party_id" x-show="partyType === 'supplier'" :disabled="partyType !== 'supplier'"
                        class="form-select @error('party_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('party_id') == $supplier->id)>{{ $supplier->localized_name }} ({{ $supplier->code }})</option>
                    @endforeach
                </select>

                <p class="text-xs text-slate-400 mt-1.5" x-show="!partyType">{{ __('accounting.add_invoice_subtitle') }}</p>
                @error('party_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('accounting.invoice_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('accounting.invoice_due_date') }}</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}"
                       class="form-input @error('due_date') is-invalid @enderror">
                @error('due_date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('accounting.invoice_currency') }}</label>
                <select name="currency_id" class="form-select @error('currency_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(old('currency_id', $currencies->firstWhere('is_default', true)?->id) == $currency->id)>{{ $currency->localized_name }}</option>
                    @endforeach
                </select>
                @error('currency_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">{{ __('accounting.invoice_notes') }}</label>
                <textarea name="notes" rows="2" class="form-input @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list text-blue-500 text-sm"></i>
                {{ __('accounting.invoice_items') }}
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
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider">{{ __('accounting.invoice_item_description') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-32">{{ __('accounting.invoice_item_quantity') }}</th>
                        <th class="px-5 py-3 text-start text-xs font-black text-slate-500 uppercase tracking-wider w-36">{{ __('accounting.invoice_item_unit_price') }}</th>
                        <th class="px-5 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-5 py-2.5">
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="form-input" required>
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
            {{ __('accounting.add_invoice') }}
        </button>
        <a href="{{ route('accounting.invoices.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
