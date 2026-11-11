@extends('layouts.app')

@section('title', __('settings.add_sales_rep'))
@section('breadcrumb', __('settings.add_sales_rep'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.add_sales_rep') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('settings.sales-representatives.store') }}" method="POST">
    @csrf
    @include('settings.sales-representatives._form')

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('settings.add_sales_rep') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
