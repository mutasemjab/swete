@extends('layouts.app')

@section('title', __('accounting.add_supplier'))
@section('breadcrumb', __('accounting.add_supplier'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_supplier') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('accounting.suppliers.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('accounting.suppliers._form', ['groups' => $groups, 'countries' => $countries, 'governorates' => $governorates])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('accounting.add_supplier') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
