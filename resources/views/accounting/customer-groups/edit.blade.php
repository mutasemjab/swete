@extends('layouts.app')

@section('title', __('accounting.edit_group'))
@section('breadcrumb', __('accounting.edit_group'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.edit_group') }}</h1>
        <p class="page-subtitle">{{ $group->localized_name }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('accounting.customer-groups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')
    @include('accounting.customer-groups._form', ['parents' => $parents, 'group' => $group])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
