@extends('layouts.app')

@section('title', __('accounting.add_' . $type))
@section('breadcrumb', __('accounting.add_' . $type))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('accounting.add_' . $type) }}</h1>
    </div>
    <a href="{{ route("accounting.{$type}s.index") }}" class="btn-secondary">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
        {{ __('app.back_to_list') }}
    </a>
</div>

<form action="{{ route("accounting.{$type}s.store") }}" method="POST">
    @csrf
    @include('accounting.parties._form', ['groups' => $groups, 'type' => $type])

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('accounting.add_' . $type) }}
        </button>
        <a href="{{ route("accounting.{$type}s.index") }}" class="btn-secondary">{{ __('app.cancel') }}</a>
    </div>
</form>
@endsection
