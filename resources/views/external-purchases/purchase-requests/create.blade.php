@extends('layouts.app')

@section('title', __('external_purchases.add_purchase_request'))
@section('breadcrumb', __('external_purchases.add_purchase_request'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.add_purchase_request') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.add_purchase_request_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('purchase-requests.store') }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @if($reminder)
        <input type="hidden" name="reminder_id" value="{{ $reminder->id }}">
    @endif
    @include('external-purchases.purchase-requests._form', [
        'projects' => $projects, 'serviceCalls' => $serviceCalls, 'suppliers' => $suppliers,
        'branches' => $branches, 'currencies' => $currencies, 'countries' => $countries, 'governorates' => $governorates,
        'materials' => $materials, 'project' => $project, 'serviceCall' => $serviceCall,
        'reminder' => $reminder,
    ])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('external_purchases.add_purchase_request') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
