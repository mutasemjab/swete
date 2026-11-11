@extends('layouts.app')

@section('title', __('agreements.edit_agreement'))
@section('breadcrumb', __('agreements.edit_agreement'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('agreements.edit_agreement') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('agreements.update', $agreement) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="back" value="{{ $backUrl }}">
    @include('agreements._form', ['agreement' => $agreement, 'customers' => $customers, 'suppliers' => $suppliers])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
