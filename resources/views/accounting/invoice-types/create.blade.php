@extends('layouts.app')

@section('title', __('accounting.add_invoice_type'))
@section('breadcrumb', __('accounting.add_invoice_type'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_invoice_type') }}</h1>
        <p class="page-subtitle">{{ __('accounting.add_invoice_type_subtitle') }}</p>
    </div>
    <a href="{{ route('accounting.invoice-types.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('accounting.invoice-types.store') }}" method="POST">
    @csrf
    @include('accounting.invoice-types._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('accounting.add_invoice_type') }}
        </button>
        <a href="{{ route('accounting.invoice-types.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
