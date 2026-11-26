@extends('layouts.app')
@section('title', __('maintenance.notification_settings'))
@section('breadcrumb', __('maintenance.notification_settings'))
@section('content')
<div class="page-header"><div><h1 class="page-title">{{ __('maintenance.notification_settings') }}</h1><p class="page-subtitle">{{ __('maintenance.notification_settings_hint') }}</p></div></div>
<form method="POST" action="{{ route('settings.maintenance-notifications.update') }}">
    @csrf @method('PUT')
    @foreach($kinds as $kind)
    <section class="card p-6 mb-5">
        <h2 class="font-bold text-lg mb-4">{{ __('maintenance.recipients_'.$kind) }}</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach($users as $user)
            <label class="flex gap-3 border rounded-xl p-3 items-center">
                <input type="checkbox" name="{{ $kind }}[]" value="{{ $user->id }}" @checked(in_array($user->id, old($kind, ($recipients[$kind] ?? collect())->pluck('user_id')->all())))>
                <span>{{ $user->name }} <span class="text-xs text-slate-400" dir="ltr">{{ $user->email }}</span></span>
            </label>
            @endforeach
        </div>
        @error($kind)<p class="form-error">{{ $message }}</p>@enderror
    </section>
    @endforeach
    <button class="btn-primary">{{ __('app.save') }}</button>
</form>
@endsection
