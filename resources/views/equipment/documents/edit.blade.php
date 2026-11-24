@extends('layouts.app')

@section('title', __('equipment.edit_document'))
@section('breadcrumb', __('equipment.edit_document'))

@php($backUrl = \App\Support\BackNavigation::url([$document], 'documents.index'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('equipment.edit_document') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('equipment.documents._form', ['document' => $document])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
