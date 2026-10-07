@php
    $isRtl = app()->isLocale('ar');
    $dir   = $isRtl ? 'rtl' : 'ltr';
    $lang  = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>{{ __('maintenance.mobile_visit_title') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    @include('maintenance.visits._mobile-styles')
</head>
<body class="min-h-screen pb-32">

    <div class="app-bar">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="flex-1">
            <p class="font-black leading-tight">{{ __('maintenance.mobile_visit_title') }}</p>
            <p class="text-xs text-white/60">{{ config('app.name', 'ERP') }}</p>
        </div>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 active:bg-white/20" title="{{ __('app.logout') }}">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        </form>
    </div>

    <div class="max-w-lg mx-auto px-4 pt-4"
         x-data="{
            customerId: '',
            availableRequests: {{ $availableRequests->toJson() }},
            requestId: '',
            get myRequests() { return this.availableRequests[this.customerId] || []; },
         }"
         x-init="$nextTick(() => window.initSelect2()); $watch('customerId', () => { requestId = ''; $nextTick(() => window.initSelect2()); });">

        @if(session('success'))
            <div class="m-card !bg-emerald-50 !border-emerald-200 text-center py-6 mb-4">
                <p class="font-bold text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('maintenance-visits.mobile.store') }}" method="POST">
            @csrf

            <div class="m-card">
                <div class="flex items-center gap-2 mb-3">
                    <span class="m-chip bg-indigo-600 text-white">1</span>
                    <p class="font-black text-slate-800">{{ __('maintenance.mobile_choose_customer_step') }}</p>
                </div>
                <select name="customer_id" x-model="customerId" class="js-select2 m-input" required>
                    <option value="">{{ __('app.select') }}</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->localized_name }} ({{ $customer->code }})</option>
                    @endforeach
                </select>
                @error('customer_id')<p class="text-rose-500 text-xs mt-2 font-bold">{{ $message }}</p>@enderror
            </div>

            <div class="m-card">
                <div class="flex items-center gap-2 mb-3">
                    <span class="m-chip bg-indigo-600 text-white">2</span>
                    <p class="font-black text-slate-800">{{ __('maintenance.visit_check_in_at') }}</p>
                </div>
                <input type="datetime-local" name="check_in_at" value="{{ now()->format('Y-m-d\TH:i') }}" class="m-input" dir="ltr" required>
                <p class="text-[11px] text-slate-400 mt-1.5">{{ __('maintenance.visit_check_in_hint') }}</p>
            </div>

            <template x-if="myRequests.length > 0">
                <div class="m-card" x-cloak>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="m-chip bg-indigo-600 text-white">3</span>
                        <p class="font-black text-slate-800">{{ __('maintenance.visit_link_request') }}</p>
                    </div>
                    <select name="maintenance_request_id" x-model="requestId" class="js-select2 m-input">
                        <option value="">{{ __('maintenance.visit_no_linked_request') }}</option>
                        <template x-for="r in myRequests" :key="r.id">
                            <option :value="r.id" x-text="r.description"></option>
                        </template>
                    </select>
                </div>
            </template>

            <div class="fixed inset-x-0 bottom-0 z-20 bg-white/95 backdrop-blur border-t border-slate-200 px-4 py-3" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
                <div class="max-w-lg mx-auto">
                    <button type="submit" class="m-btn-primary">
                        <i class="fa-solid fa-play"></i>
                        {{ __('maintenance.visit_start') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    @include('maintenance.visits._mobile-select2-script')
</body>
</html>
