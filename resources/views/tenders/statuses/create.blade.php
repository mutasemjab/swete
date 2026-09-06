@extends('layouts.app')

@section('title', __('tenders.add_status'))
@section('breadcrumb', __('tenders.add_status'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_status') }}</h1>
    </div>
    <a href="{{ route('tender-statuses.index') }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route('tender-statuses.store') }}" method="POST">
    @csrf
    @include('tenders.statuses._form', ['colors' => $colors])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_status') }}
        </button>
        <a href="{{ route('tender-statuses.index') }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
