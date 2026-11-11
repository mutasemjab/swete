@extends('layouts.app')

@section('title', __('tenders.add_reminder'))
@section('breadcrumb', __('tenders.add_reminder'))

@php($backUrl = \App\Support\BackNavigation::url())

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_reminder') }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_reminder_subtitle') }}</p>
    </div>
    @include('components.back-link', ['url' => $backUrl])
</div>

<form action="{{ route('purchase-request-reminders.store') }}" method="POST">
    @csrf
    <input type="hidden" name="back" value="{{ $backUrl }}">

    @include('tenders.purchase-request-reminders._form', ['reminder' => null, 'projects' => $projects, 'materials' => $materials])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-paper-plane"></i>
            {{ __('tenders.add_reminder') }}
        </button>
        <a href="{{ $backUrl }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
