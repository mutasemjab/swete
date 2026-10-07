@php
    $isRtl = app()->isLocale('ar');
    $dir   = $isRtl ? 'rtl' : 'ltr';
    $lang  = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', __('customer_portal.portal_title')) — {{ config('app.name', 'ERP') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        * { font-family: 'Cairo', sans-serif; }
        body { background: #f1f5f9; }
        [x-cloak] { display: none !important; }
        .cp-bar  { @apply sticky top-0 z-20 bg-slate-900 text-white px-5 py-4 flex items-center gap-3 shadow-md; }
        .cp-card { @apply bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-5; }
        .cp-label { @apply block text-sm font-bold text-slate-700 mb-2; }
        .cp-input { @apply w-full px-4 py-3 border border-slate-200 rounded-xl text-base text-slate-800 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 focus:bg-white transition-all; }
        .cp-btn-primary { @apply inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold text-sm bg-indigo-600 text-white active:bg-indigo-800 shadow-lg shadow-indigo-500/30 disabled:opacity-40 transition-all; }
        .cp-btn-outline { @apply inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold text-sm bg-white text-slate-700 border-2 border-slate-200 active:bg-slate-100 transition-all; }
    </style>
</head>
<body class="min-h-screen pb-12">
    @hasSection('bar')
        @yield('bar')
    @endif

    <div class="max-w-3xl mx-auto px-4 pt-6">
        @if(session('success'))
            <div class="cp-card !bg-emerald-50 !border-emerald-200">
                <p class="font-bold text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
