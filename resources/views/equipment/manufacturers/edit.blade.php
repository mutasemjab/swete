@extends('layouts.app')

@section('title', __('equipment.edit_manufacturer'))
@section('breadcrumb', __('equipment.edit_manufacturer'))

@php($backUrl = \App\Support\BackNavigation::url([$manufacturer], 'equipment-manufacturers.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_manufacturer') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('equipment-manufacturers.update', $manufacturer) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.manufacturers._form', ['manufacturer' => $manufacturer])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
