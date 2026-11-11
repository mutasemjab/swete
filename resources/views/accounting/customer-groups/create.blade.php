@extends('layouts.app')

@section('title', __('accounting.add_group_customer'))
@section('breadcrumb', __('accounting.add_group_customer'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_group_customer') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('accounting.customer-groups.store') }}" method="POST">
    @csrf
    @include('accounting.customer-groups._form', ['parents' => $parents])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('accounting.add_group_customer') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
