@extends('layouts.app')

@section('title', __('warehouse.add_unit'))
@section('breadcrumb', __('warehouse.add_unit'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_unit') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_unit_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('warehouse.units.store') }}" method="POST">
    @csrf
    @include('warehouse.units._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_unit') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
