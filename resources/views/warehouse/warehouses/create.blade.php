@extends('layouts.app')

@section('title', __('warehouse.add_warehouse'))
@section('breadcrumb', __('warehouse.add_warehouse'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_warehouse') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_warehouse_subtitle') }}</p>
    </div>
    <a href="{{ route('warehouse.warehouses.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('warehouse.warehouses.store') }}" method="POST">
    @csrf
    @include('warehouse.warehouses._form', ['branches' => $branches])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_warehouse') }}
        </button>
        <a href="{{ route('warehouse.warehouses.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
