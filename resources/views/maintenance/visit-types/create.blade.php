@extends('layouts.app')

@section('title', __('maintenance.add_visit_type'))
@section('breadcrumb', __('maintenance.add_visit_type'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_visit_type') }}</h1>
    </div>
    <a href="{{ route('maintenance-visit-types.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('maintenance-visit-types.store') }}" method="POST">
    @csrf
    @include('maintenance.visit-types._form', ['visitType' => null])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_visit_type') }}
        </button>
        <a href="{{ route('maintenance-visit-types.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
