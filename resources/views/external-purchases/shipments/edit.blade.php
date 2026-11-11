@extends('layouts.app')

@section('title', __('external_purchases.edit_shipment'))
@section('breadcrumb', __('external_purchases.edit_shipment'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.edit_shipment') }}</h1>
        <p class="page-subtitle">{{ $shipment->number }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('shipments.update', $shipment) }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @method('PUT')
    @include('external-purchases.shipments._form', [
        'shipment' => $shipment,
        'eligiblePurchaseRequests' => $eligiblePurchaseRequests,
        'shippingCompanies' => $shippingCompanies,
        'currencies' => $currencies,
    ])

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
                        action: '{{ route('shipments.destroy', $shipment) }}',
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
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
