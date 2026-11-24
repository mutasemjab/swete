@extends('layouts.app')

@section('title', __('equipment.add_document_type'))
@section('breadcrumb', __('equipment.add_document_type'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.add_document_type') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('document-types.store') }}" method="POST">
    @csrf
    @include('equipment.document-types._form', ['type' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('equipment.add_document_type') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
