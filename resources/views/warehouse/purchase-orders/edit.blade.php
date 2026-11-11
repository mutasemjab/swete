@extends('layouts.app')

@section('title', __('warehouse.edit_purchase_order'))
@section('breadcrumb', __('warehouse.edit_purchase_order'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.edit_purchase_order') }}</h1>
        <p class="page-subtitle">{{ $order->number }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('purchase-orders.update', $order) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @include('warehouse.purchase-orders._form', ['order' => $order, 'suppliers' => $suppliers, 'warehouses' => $warehouses, 'materials' => $materials, 'materialCategories' => $materialCategories, 'units' => $units, 'backUrl' => $backUrl])
</form>
@endsection
