@extends('layouts.app')

@section('title', __('tenders.add_quote'))
@section('breadcrumb', __('tenders.add_quote'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_quote') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_quote_subtitle') }}</p>
    </div>
    <a href="{{ $tender ? route('tenders.show', $tender) : route('price-quotes.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('price-quotes.store') }}" method="POST"
      x-data="{
        items: [{ material_id: '', quantity: '', unit_price: '' }],
        addItem() { this.items.push({ material_id: '', quantity: '', unit_price: '' }); this.$nextTick(() => window.initSelect2()); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
      }"
      x-init="$nextTick(() => window.initSelect2())">
    @csrf
    @if($tender)
        <input type="hidden" name="tender_id" value="{{ $tender->id }}">
    @endif

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-orange-500 text-sm"></i>
                {{ __('tenders.price_quote') }}
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            @if($tender)
            <div class="sm:col-span-2">
                <label class="form-label">{{ __('tenders.quote_tender') }}</label>
                <p class="font-bold text-slate-800">{{ $tender->number }} — {{ $tender->localized_title }}</p>
            </div>
            @endif

            <div>
                <label class="form-label">{{ __('accounting.customer') }} <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="js-select2 form-select @error('customer_id') is-invalid @enderror">
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $tender?->party_id) == $customer->id)>{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('tenders.quote_date') }} <span class="text-rose-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                       class="form-input @error('date') is-invalid @enderror">
                @error('date')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
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
                <i class="fa-solid fa-list text-orange-500 text-sm"></i>
                {{ __('tenders.quote_items') }}
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
            {{ __('tenders.add_quote') }}
        </button>
        <a href="{{ $tender ? route('tenders.show', $tender) : route('price-quotes.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
