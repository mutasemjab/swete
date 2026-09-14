@extends('layouts.app')

@section('title', __('settings.purchase_request_reminder_recipients'))
@section('breadcrumb', __('settings.purchase_request_reminder_recipients'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('settings.purchase_request_reminder_recipients') }}</h1>
        <p class="page-subtitle">{{ __('settings.purchase_request_reminder_recipients_subtitle') }}</p>
    </div>
</div>

<form action="{{ route('settings.purchase-request-reminder-recipients.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-bell text-indigo-500 text-sm"></i>
                {{ __('settings.purchase_request_reminder_recipients') }}
            </h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-xs text-slate-400 mb-4">{{ __('settings.purchase_request_reminder_recipients_hint') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($users as $user)
                    <label class="flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 cursor-pointer hover:border-indigo-300 transition-colors">
                        <input type="checkbox" name="recipient_ids[]" value="{{ $user->id }}"
                               @checked($currentRecipientIds->contains($user->id))>
                        <span>
                            <span class="block font-bold text-slate-800">{{ $user->name }}</span>
                            <span class="block text-xs text-slate-400" dir="ltr">{{ $user->email }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ __('app.save') }}
        </button>
    </div>
</form>
@endsection
