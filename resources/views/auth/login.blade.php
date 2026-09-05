@extends('layouts.auth')

@section('title', __('app.login_title'))

@section('content')
@php $isRtl = app()->isLocale('ar'); @endphp

<div class="bg-white rounded-3xl shadow-2xl shadow-slate-900/10 border border-slate-100 overflow-hidden"
     style="animation: slideUp 0.5s cubic-bezier(0.34,1.56,0.64,1) both;">

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>

    {{-- Card header --}}
    <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-700 px-8 py-8 text-center relative overflow-hidden">
        <div class="absolute -top-8 -end-8 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-6 -start-6 w-24 h-24 bg-violet-400/20 rounded-full pointer-events-none"></div>

        <div class="relative">
            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fa-solid fa-diagram-project text-white text-2xl"></i>
            </div>
            <h1 class="text-white font-black text-2xl leading-tight">{{ __('app.erp_name') }}</h1>
            <p class="text-white/60 text-sm mt-1.5 font-medium">{{ __('app.erp_subtitle') }}</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="px-8 py-8">
        <div class="mb-7">
            <h2 class="text-xl font-black text-slate-800">{{ __('app.login_title') }}</h2>
            <p class="text-slate-500 text-sm mt-1">{{ __('app.login_subtitle') }}</p>
        </div>

        <form action="{{ route('auth.authenticate') }}" method="POST" x-data="{ showPass: false }">
            @csrf

            {{-- Email --}}
            <div class="mb-5">
                <label class="form-label" for="email">{{ __('app.email') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 start-3.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                    </span>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           autocomplete="email"
                           autofocus
                           placeholder="user@example.com"
                           dir="ltr"
                           class="form-input ps-10 {{ $errors->has('email') ? 'is-invalid' : '' }}">
                </div>
                @error('email')
                    <p class="flex items-center gap-1.5 text-rose-500 text-xs mt-2 font-semibold">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <label class="form-label" for="password">{{ __('app.password') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 start-3.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                    </span>
                    <input id="password"
                           :type="showPass ? 'text' : 'password'"
                           name="password"
                           autocomplete="current-password"
                           placeholder="••••••••"
                           dir="ltr"
                           class="form-input ps-10 pe-10 {{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <button type="button"
                            @click="showPass = !showPass"
                            class="absolute inset-y-0 end-3.5 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i :class="showPass ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-sm"></i>
                    </button>
                </div>
                @error('password')
                    <p class="flex items-center gap-1.5 text-rose-500 text-xs mt-2 font-semibold">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-3 mb-7">
                <input id="remember"
                       type="checkbox"
                       name="remember"
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">
                    {{ __('app.remember_me') }}
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2.5 px-6 py-3.5 bg-indigo-600
                           text-white font-black text-base rounded-xl
                           hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-500/30
                           active:scale-[0.98] transition-all duration-200">
                <i class="fa-solid fa-right-to-bracket {{ $isRtl ? 'fa-flip-horizontal' : '' }}"></i>
                {{ __('app.login_btn') }}
            </button>
        </form>
    </div>

    {{-- Footer --}}
    <div class="border-t border-slate-100 px-8 py-4 bg-slate-50/60 text-center">
        <p class="text-xs text-slate-400 font-medium">
            {{ __('app.erp_name') }} &mdash; {{ __('app.all_rights') }} &copy; {{ date('Y') }}
        </p>
    </div>
</div>
@endsection
