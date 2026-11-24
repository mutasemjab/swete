@extends('layouts.app')

@section('title', __('equipment.edit_document_type'))
@section('breadcrumb', __('equipment.edit_document_type'))

@php($backUrl = \App\Support\BackNavigation::url([$type], 'document-types.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_document_type') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('document-types.update', $type) }}" method="POST">
    @csrf
    @method('PUT')
    @include('equipment.document-types._form', ['type' => $type])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
