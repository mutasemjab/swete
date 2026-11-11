@extends('layouts.app')

@section('title', __('maintenance.add_visit_type'))
@section('breadcrumb', __('maintenance.add_visit_type'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_visit_type') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('maintenance-visit-types.store') }}" method="POST">
    @csrf
    @include('maintenance.visit-types._form', ['visitType' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_visit_type') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
