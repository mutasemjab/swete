@extends('layouts.app')

@section('title', __('external_purchases.add_shipment'))
@section('breadcrumb', __('external_purchases.add_shipment'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.add_shipment') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.add_shipment_subtitle') }}</p>
    </div>
    <a href="{{ route('shipments.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('shipments.store') }}" method="POST">
    @csrf
    @include('external-purchases.shipments._form', [
        'eligiblePurchaseRequests' => $eligiblePurchaseRequests,
        'shippingCompanies' => $shippingCompanies,
        'currencies' => $currencies,
    ])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('external_purchases.add_shipment') }}
        </button>
        <a href="{{ route('shipments.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
