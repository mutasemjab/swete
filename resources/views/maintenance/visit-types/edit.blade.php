@extends('layouts.app')

@section('title', __('maintenance.edit_visit_type'))
@section('breadcrumb', __('maintenance.edit_visit_type'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_visit_type') }}</h1>
    </div>
    <a href="{{ route('maintenance-visit-types.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-visit-types.update', $visitType) }}" method="POST">
    @csrf
    @method('PUT')
    @include('maintenance.visit-types._form', ['visitType' => $visitType])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('maintenance-visit-types.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
