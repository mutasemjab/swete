@extends('layouts.app')

@section('title', __('equipment.edit_unit'))
@section('breadcrumb', __('equipment.edit_unit'))

@php($backUrl = \App\Support\BackNavigation::url([$unit], 'equipment-units.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_unit') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('equipment-units.update', $unit) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.units._form', ['unit' => $unit])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
