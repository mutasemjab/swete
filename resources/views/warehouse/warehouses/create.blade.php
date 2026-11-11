@extends('layouts.app')

@section('title', __('warehouse.add_warehouse'))
@section('breadcrumb', __('warehouse.add_warehouse'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_warehouse') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_warehouse_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('warehouse.warehouses.store') }}" method="POST">
    @csrf
    @include('warehouse.warehouses._form', ['branches' => $branches])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_warehouse') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
