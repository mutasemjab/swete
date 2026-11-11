@extends('layouts.app')

@section('title', __('external_purchases.add_shipment'))
@section('breadcrumb', __('external_purchases.add_shipment'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.add_shipment') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.add_shipment_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('shipments.store') }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
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
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
