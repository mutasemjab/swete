<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="theme-color" content="#102c37"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('maintenance.mobile_visit_title') }}</title>
    <link rel="stylesheet" href="{{ url('/assets/maintenance/technician.css') }}">
</head>
<body>
    <header class="app-header"><div class="brand-mark">A</div><div><strong>ASEE</strong><small>Field service · الصيانة الميدانية</small></div><div class="header-actions"><span id="connection"></span><button id="language" type="button">EN / عربي</button></div></header>
    <div id="notice" role="status" aria-live="polite"></div>
    <nav id="navigation" aria-label="Navigation"></nav>
    <main id="workspace"><p class="loading">جاري تحميل مساحة العمل… / Loading workspace…</p></main>
    <script id="technician-config" type="application/json">{!! json_encode([
        'user' => ['id' => Auth::id(), 'name' => Auth::user()->name], 'locale' => app()->getLocale(),
        'initialVisitId' => $initialVisitId, 'base' => url('/m/visits'),
        'assets' => url('/assets/maintenance'), 'csrf' => csrf_token(),
        'logout' => route('auth.logout'), 'login' => url('/login'),
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="module" src="{{ url('/assets/maintenance/technician.js') }}"></script>
</body>
</html>
