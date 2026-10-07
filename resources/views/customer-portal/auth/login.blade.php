@extends('layouts.customer-portal')

@section('title', __('customer_portal.login_title'))

@section('content')
<div class="flex items-center justify-center" style="min-height: 80vh;">
    <div class="cp-card w-full max-w-sm">
        <div class="text-center mb-5">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-screwdriver-wrench text-white text-xl"></i>
            </div>
            <p class="font-black text-lg text-slate-800">{{ config('app.name', 'ERP') }}</p>
            <p class="text-sm text-slate-500">{{ __('customer_portal.login_subtitle') }}</p>
        </div>

        @error('code')
            <p class="text-rose-600 text-sm font-bold text-center mb-4">{{ $message }}</p>
        @enderror

        <form method="POST" action="{{ route('customer-portal.authenticate') }}" class="space-y-4">
            @csrf
            <div>
                <label class="cp-label">{{ __('customer_portal.customer_code') }}</label>
                <input type="text" name="code" value="{{ old('code') }}" dir="ltr" class="cp-input" required autofocus>
            </div>
            <div>
                <label class="cp-label">{{ __('app.password') }}</label>
                <input type="password" name="password" class="cp-input" required>
            </div>
            <button type="submit" class="cp-btn-primary w-full justify-center">
                <i class="fa-solid fa-right-to-bracket"></i>
                {{ __('app.login_btn') }}
            </button>
        </form>
    </div>
</div>
@endsection
