@extends('layouts.app')

@section('title', __('equipment.edit_category'))
@section('breadcrumb', __('equipment.edit_category'))

@php($backUrl = \App\Support\BackNavigation::url([$category], 'equipment-categories.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_category') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('equipment-categories.update', $category) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.categories._form', ['category' => $category])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
