@extends('layouts.app')

@section('title', __('external_purchases.vendor_email_template'))
@section('breadcrumb', __('external_purchases.vendor_email_template'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('external_purchases.vendor_email_template') }}</h1>
        <p class="page-subtitle">{{ __('external_purchases.vendor_email_template_subtitle') }}</p>
    </div>
</div>

<form action="{{ route('vendor-email-template.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card mb-5">
        <div class="card-header">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-envelope-open-text text-cyan-500 text-sm"></i>
                {{ __('external_purchases.vendor_email_template') }}
            </h3>
        </div>
        <div class="px-6 py-5 space-y-5">
            <p class="text-xs text-slate-400">{{ __('external_purchases.vendor_email_template_placeholder_hint') }}</p>

            <div>
                <label class="form-label">{{ __('external_purchases.email_subject') }}</label>
                <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" dir="ltr"
                       class="form-input @error('subject') is-invalid @enderror">
                @error('subject')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">{{ __('external_purchases.email_body') }}</label>
                <textarea name="body" rows="10" dir="ltr" class="form-input @error('body') is-invalid @enderror">{{ old('body', $template->body) }}</textarea>
                @error('body')<p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>@enderror
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
