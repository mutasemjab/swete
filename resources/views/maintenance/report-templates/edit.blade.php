@extends('layouts.app')

@section('title', __('maintenance.edit_template'))
@section('breadcrumb', __('maintenance.edit_template'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('maintenance.edit_template') }}</h1>
        <p class="page-subtitle">{{ $template->localized_name }}</p>
    </div>
    <a href="{{ route('report-templates.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<div class="card px-5 py-4 mb-5 bg-amber-50 border-amber-100 flex items-start gap-3">
    <i class="fa-solid fa-circle-info text-amber-500 mt-0.5"></i>
    <p class="text-sm text-amber-800">{{ __('maintenance.edit_template_hint') }}</p>
</div>

<form action="{{ route('report-templates.update', $template) }}" method="POST">
    @csrf
    @method('PUT')
    @include('maintenance.report-templates._form', ['template' => $template, 'materials' => $materials])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('report-templates.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
