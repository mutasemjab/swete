@extends('layouts.app')

@section('title', __('equipment.edit_subtype'))
@section('breadcrumb', __('equipment.edit_subtype'))

@php($backUrl = \App\Support\BackNavigation::url([$subtype], 'equipment-subtypes.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_subtype') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('equipment-subtypes.update', $subtype) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.subtypes._form', ['subtype' => $subtype])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
