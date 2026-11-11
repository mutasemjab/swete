@extends('layouts.app')

@section('title', __('accounting.add_group_supplier'))
@section('breadcrumb', __('accounting.add_group_supplier'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_group_supplier') }}</h1>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('accounting.supplier-groups.store') }}" method="POST">
    @csrf
    @include('accounting.supplier-groups._form', ['parents' => $parents])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('accounting.add_group_supplier') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
