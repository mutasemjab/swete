@php $routePrefix = $type === 'service_call' ? 'service-calls' : 'tenders'; @endphp
@extends('layouts.app')

@section('title', __('tenders.add_' . $type))
@section('breadcrumb', __('tenders.add_' . $type))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('tenders.add_' . $type) }}</h1>
        <p class="page-subtitle">{{ __('tenders.add_tender_subtitle') }}</p>
    </div>
    <a href="{{ route("{$routePrefix}.index") }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route("{$routePrefix}.store") }}" method="POST">
    @csrf
    @include('tenders._form', ['type' => $type, 'customers' => $customers])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('tenders.add_' . $type) }}
        </button>
        <a href="{{ route("{$routePrefix}.index") }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
