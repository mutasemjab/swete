@extends('layouts.app')

@section('title', __('warehouse.edit_warehouse'))
@section('breadcrumb', __('warehouse.edit_warehouse'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.edit_warehouse') }}</h1>
        <p class="page-subtitle">{{ $warehouse->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('warehouse.warehouses.update', $warehouse) }}" method="POST">
    @csrf
    @method('PUT')
    @include('warehouse.warehouses._form', ['branches' => $branches, 'warehouse' => $warehouse])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
