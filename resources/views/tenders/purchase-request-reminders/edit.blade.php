@extends('layouts.app')

@section('title', __('tenders.edit_reminder'))
@section('breadcrumb', __('tenders.edit_reminder'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.edit_reminder') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_reminder_subtitle') }}</p>
    </div>
    <a href="{{ route('purchase-request-reminders.show', $reminder) }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('purchase-request-reminders.update', $reminder) }}" method="POST">
    @csrf
    @method('PUT')

    @include('tenders.purchase-request-reminders._form', ['reminder' => $reminder, 'projects' => $projects, 'materials' => $materials])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
        <a href="{{ route('purchase-request-reminders.show', $reminder) }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
