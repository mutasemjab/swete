@extends('layouts.app')

@section('title', __('warehouse.edit_category'))
@section('breadcrumb', __('warehouse.edit_category'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('warehouse.edit_category') }}</h1>
        <p class="page-subtitle">{{ $category->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('warehouse.categories.update', $category) }}" method="POST">
    @csrf
    @method('PUT')
    @include('warehouse.categories._form', ['parents' => $parents, 'category' => $category])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
