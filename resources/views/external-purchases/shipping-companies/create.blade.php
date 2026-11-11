@extends('layouts.app')

@section('title', __('external_purchases.add_shipping_company'))
@section('breadcrumb', __('external_purchases.add_shipping_company'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.add_shipping_company') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.add_shipping_company_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('shipping-companies.store') }}" method="POST">
    @csrf
    @include('external-purchases.shipping-companies._form', ['countries' => $countries])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('external_purchases.add_shipping_company') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
