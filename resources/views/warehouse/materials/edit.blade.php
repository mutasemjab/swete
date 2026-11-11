@extends('layouts.app')

@section('title', __('warehouse.edit_material'))
@section('breadcrumb', __('warehouse.edit_material'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.edit_material') }}</h1>
        <p class="page-subtitle">{{ $material->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('warehouse.materials.update', $material) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('warehouse.materials._form', ['categories' => $categories, 'units' => $units, 'material' => $material])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
