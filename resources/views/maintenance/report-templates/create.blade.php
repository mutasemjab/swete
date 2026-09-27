@extends('layouts.app')

@section('title', __('maintenance.add_template'))
@section('breadcrumb', __('maintenance.add_template'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.add_template') }}</h1>
        <p class="page-subtitle">{{ __('maintenance.add_template_subtitle') }}</p>
    </div>
    <a href="{{ route('report-templates.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('report-templates.store') }}" method="POST">
    @csrf
    @include('maintenance.report-templates._form', ['template' => null, 'materials' => $materials])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('maintenance.add_template') }}
        </button>
        <a href="{{ route('report-templates.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
