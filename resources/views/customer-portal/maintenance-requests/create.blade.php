@extends('layouts.customer-portal')

@section('title', __('customer_portal.new_request'))

@section('bar')
<div class="cp-bar">
    <a href="{{ route('customer-portal.dashboard') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    </a>
    <div class="flex-1">
        <p class="font-black leading-tight">{{ __('customer_portal.new_request') }}</p>
    </div>
</div>
@endsection

@section('content')
<div class="cp-card">
    <form method="POST" action="{{ route('customer-portal.maintenance-requests.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="cp-label">{{ __('maintenance.request_description') }} <span class="text-rose-500">*</span></label>
            <textarea name="description" rows="4" class="cp-input @error('description') border-rose-400 @enderror">{{ old('description') }}</textarea>
            @error('description')<p class="text-rose-600 text-sm font-bold mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="cp-label">{{ __('maintenance.request_preferred_date') }}</label>
            <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" dir="ltr" class="cp-input">
        </div>
        <button type="submit" class="cp-btn-primary w-full justify-center">
            <i class="fa-solid fa-paper-plane"></i>
            {{ __('customer_portal.submit_request') }}
        </button>
    </form>
</div>
@endsection
