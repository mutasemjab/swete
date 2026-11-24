@extends('layouts.app')

@section('title', __('equipment.add_document'))
@section('breadcrumb', __('equipment.add_document'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.add_document') }}</h1>
        <p class="page-subtitle">{{ __('equipment.add_document_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('equipment.documents._form', ['document' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('equipment.add_document') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
