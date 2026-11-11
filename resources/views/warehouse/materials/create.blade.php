@extends('layouts.app')

@section('title', __('warehouse.add_material'))
@section('breadcrumb', __('warehouse.add_material'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.add_material') }}</h1>
        <p class="page-subtitle">{{ __('warehouse.add_material_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('warehouse.materials.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('warehouse.materials._form', ['categories' => $categories, 'units' => $units])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('warehouse.add_material') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
