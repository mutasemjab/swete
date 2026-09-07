@extends('layouts.app')

@section('title', __('external_purchases.edit_purchase_request'))
@section('breadcrumb', __('external_purchases.edit_purchase_request'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.edit_purchase_request') }}</h1>
        <p class="page-subtitle">{{ $purchaseRequest->number }}</p>
    </div>
    <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('purchase-requests.update', $purchaseRequest) }}" method="POST">
    @csrf
    @method('PUT')
    @include('external-purchases.purchase-requests._form', [
        'purchaseRequest' => $purchaseRequest,
        'projects' => $projects, 'serviceCalls' => $serviceCalls, 'suppliers' => $suppliers,
        'branches' => $branches, 'currencies' => $currencies, 'countries' => $countries,
        'materials' => $materials, 'project' => $project, 'serviceCall' => $serviceCall,
    ])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
