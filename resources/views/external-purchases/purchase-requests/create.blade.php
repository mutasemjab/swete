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

<form action="{{ route('purchase-requests.store') }}" method="POST">
    @csrf
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
        <a href="{{ $project ? route('projects.show', $project) : route('purchase-requests.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
