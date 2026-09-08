@extends('layouts.app')

@section('title', __('external_purchases.edit_shipping_company'))
@section('breadcrumb', __('external_purchases.edit_shipping_company'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.edit_shipping_company') }}</h1>
        <p class="page-subtitle">{{ $shippingCompany->localized_name }}</p>
    </div>
    <a href="{{ route('shipping-companies.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('shipping-companies.update', $shippingCompany) }}" method="POST">
    @csrf
    @method('PUT')
    @include('external-purchases.shipping-companies._form', ['shippingCompany' => $shippingCompany, 'countries' => $countries])

    <div class="card mb-5 border-rose-200">
        <div class="card-header border-rose-100">
            <h3 class="font-bold text-rose-700 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                {{ __('app.danger_zone') }}
            </h3>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <p class="text-xs text-slate-400">{{ __('app.irreversible') }}</p>
            <button type="button"
                    @click="$dispatch('delete-confirm', {
                        action: '{{ route('shipping-companies.destroy', $shippingCompany) }}',
                        message: '{{ __('app.delete_confirm_msg') }}'
                    })"
                    class="btn-danger btn-sm flex-shrink-0">
                <i class="fa-solid fa-trash"></i>
                {{ __('app.delete') }}
            </button>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('shipping-companies.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
