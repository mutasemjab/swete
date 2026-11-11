@extends('layouts.app')

@section('title', __('tenders.add_ciat_discount'))
@section('breadcrumb', __('tenders.add_ciat_discount'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_ciat_discount') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('ciat-discounts.store') }}" method="POST">
    @csrf
    @include('tenders.ciat-discounts._form', ['discount' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_ciat_discount') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
