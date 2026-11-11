@extends('layouts.app')

@section('title', __('warehouse.add_purchase_order'))
@section('breadcrumb', __('warehouse.add_purchase_order'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_purchase_order') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('purchase-orders.store') }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @include('warehouse.purchase-orders._form', ['order' => null, 'suppliers' => $suppliers, 'warehouses' => $warehouses, 'materials' => $materials, 'materialCategories' => $materialCategories, 'units' => $units, 'backUrl' => $backUrl])
</form>
@endsection
