@extends('layouts.app')

@section('title', __('maintenance.edit_visit_type'))
@section('breadcrumb', __('maintenance.edit_visit_type'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_visit_type') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('maintenance-visit-types.update', $visitType) }}" method="POST">
    @csrf
    @method('PUT')
    @include('maintenance.visit-types._form', ['visitType' => $visitType])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
