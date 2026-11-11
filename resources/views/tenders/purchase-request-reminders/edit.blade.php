@extends('layouts.app')

@section('title', __('tenders.edit_reminder'))
@section('breadcrumb', __('tenders.edit_reminder'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_reminder') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_reminder_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('purchase-request-reminders.update', $reminder) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="back" value="{{ $backUrl }}">

    @include('tenders.purchase-request-reminders._form', ['reminder' => $reminder, 'projects' => $projects, 'materials' => $materials])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
