@extends('layouts.app')

@section('title', __('accounting.edit_invoice_type'))
@section('breadcrumb', __('accounting.edit_invoice_type'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.edit_invoice_type') }}</h1>
        <p class="page-subtitle">{{ $invoiceType->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('accounting.invoice-types.update', $invoiceType) }}" method="POST">
    @csrf
    @method('PUT')
    @include('accounting.invoice-types._form', ['invoiceType' => $invoiceType])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
