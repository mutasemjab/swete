@extends('layouts.app')

@section('title', __('accounting.edit_customer'))
@section('breadcrumb', __('accounting.edit_customer'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.edit_customer') }}</h1>
        <p class="page-subtitle">{{ $customer->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('accounting.customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('accounting.customers._form', ['groups' => $groups, 'customer' => $customer])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
