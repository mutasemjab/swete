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
    <title>{{ __('app.dashboard') }} | {{ __('app.erp_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        * { font-family: 'Cairo', sans-serif; }
    </style>

    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .module-card {
            animation: fadeUp 0.5s ease-out both;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -8px rgba(79, 70, 229, 0.12), 0 8px 16px -4px rgba(0,0,0,0.06);
        }
        .module-card:hover .card-icon-wrap {
            background-color: #4f46e5;
        }
        .module-card:hover .card-icon-wrap i {
            color: #ffffff;
        }
        .module-card:hover .card-arrow {
            transform: {{ $isRtl ? 'translateX(4px)' : 'translateX(-4px)' }};
            color: #4f46e5;
        }
        .card-icon-wrap {
            transition: background-color 0.3s ease;
        }
        .card-icon-wrap i {
            transition: color 0.3s ease;
        }
        .card-arrow {
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .hero-text { animation: fadeIn 0.6s ease-out 0.1s both; }
        .disabled-card { opacity: 0.45; pointer-events: none; }
    </style>
</head>
<body class="min-h-screen bg-slate-50" x-data>

{{-- ═══ HEADER ═══════════════════════════════════════════════════════════════ --}}
<header class="bg-slate-900 relative">

    {{-- Subtle texture overlay --}}
    <div class="absolute inset-0 opacity-30 overflow-hidden pointer-events-none"
         style="background-image: radial-gradient(circle at 20% 50%, rgba(99,102,241,0.3) 0%, transparent 50%),
                                  radial-gradient(circle at 80% 20%, rgba(139,92,246,0.2) 0%, transparent 40%);"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-5">

        {{-- Topbar --}}
        <div class="flex items-center justify-between">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-900/50">
                    <i class="fa-solid fa-diagram-project text-white text-base"></i>
                </div>
                <div>
                    <h1 class="text-white font-black text-lg leading-none">{{ __('app.erp_name') }}</h1>
                    <p class="text-slate-400 text-[11px] mt-0.5 font-medium">{{ __('app.erp_subtitle') }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">

                {{-- Clock --}}
                <div class="hidden md:flex flex-col items-end me-2">
                    <span class="text-white font-semibold text-sm" id="hdr-time"></span>
                    <span class="text-slate-400 text-xs font-medium" id="hdr-date"></span>
                </div>

                {{-- Language --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-1.5 bg-white/10 hover:bg-white/20
                                   rounded-xl px-3 py-2 transition-all text-white text-sm font-semibold border border-white/10">
                        <i class="fa-solid fa-globe text-xs text-slate-300"></i>
                        <span>{{ $isRtl ? 'AR' : 'EN' }}</span>
                    </button>
                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute end-0 top-full mt-2 w-32 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50">
                        <a href="{{ route('lang.switch', 'ar') }}"
                           class="block px-4 py-2 text-sm hover:bg-slate-50 transition-colors
                                  {{ $isRtl ? 'text-indigo-600 font-bold' : 'text-slate-700' }}">العربية</a>
                        <a href="{{ route('lang.switch', 'en') }}"
                           class="block px-4 py-2 text-sm hover:bg-slate-50 transition-colors
                                  {{ !$isRtl ? 'text-indigo-600 font-bold' : 'text-slate-700' }}">English</a>
                    </div>
                </div>

                {{-- User --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 bg-white/10 hover:bg-white/20
                                   rounded-xl px-3 py-2 transition-all border border-white/10">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center
                                    text-white font-black text-xs flex-shrink-0">
                            {{ Auth::user()->initials }}
                        </div>
                        <div class="text-start hidden sm:block">
                            <p class="text-white font-semibold text-sm leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-slate-400 text-[11px] font-medium">{{ Auth::user()->role_label }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"
                           :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute end-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-[9999]">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5 truncate" dir="ltr">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('settings.users.show', Auth::id()) }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-user w-4 text-center text-slate-400 text-sm"></i>
                            {{ __('app.profile') }}
                        </a>
                        <a href="{{ route('settings.users.index') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-sliders w-4 text-center text-slate-400 text-sm"></i>
                            {{ __('settings.module_name') }}
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center text-sm {{ $isRtl ? 'fa-flip-horizontal' : '' }}"></i>
                                {{ __('app.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Greeting --}}
        <div class="hero-text mt-8 mb-7">
            <p class="text-indigo-400 font-semibold text-sm mb-2 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full inline-block animate-pulse"></span>
                {{ __('app.welcome_msg') }}
            </p>
            <h2 class="text-white font-black text-3xl sm:text-4xl leading-tight">
                <span id="greeting"></span>،
                <span class="text-indigo-300">{{ explode(' ', Auth::user()->name)[0] }}</span>
            </h2>
        </div>
    </div>
</header>

{{-- ═══ STATS + MODULES ══════════════════════════════════════════════════════ --}}
<main class="max-w-7xl mx-auto px-6 py-8">

    {{-- Quick stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
        @php
            $stats = [
                ['label' => __('app.users_module'),  'value' => \App\Models\User::count(),                       'icon' => 'users'],
                ['label' => __('app.active_label'),  'value' => \App\Models\User::where('status', true)->count(), 'icon' => 'circle-check'],
                ['label' => __('app.sections_label'),'value' => count($modules),                                  'icon' => 'grid-2'],
                ['label' => __('app.version'),        'value' => 'v1.0',                                          'icon' => 'code-branch'],
            ];
        @endphp
        @foreach($stats as $i => $stat)
            <div class="bg-white rounded-2xl border border-slate-100 px-5 py-4 flex items-center gap-3.5 shadow-sm"
                 style="animation: fadeUp 0.4s ease-out {{ $i * 60 }}ms both;">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-{{ $stat['icon'] }} text-slate-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-xl font-black text-slate-800 leading-none">{{ $stat['value'] }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Section title --}}
    <div class="flex items-center gap-3 mb-5">
        <h3 class="text-base font-black text-slate-700 uppercase tracking-wider">{{ __('app.system_modules') }}</h3>
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">{{ count($modules) }}</span>
    </div>

    {{-- Module cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($modules as $key => $mod)
            @php
                $routeExists = $mod['route'] !== '#' && \Illuminate\Support\Facades\Route::has($mod['route']);
                $href        = $routeExists ? route($mod['route']) : '#';
                $modName     = __($mod['name']);
                $modDesc     = __($mod['description']);
            @endphp

            <a href="{{ $href }}"
               class="module-card {{ !$routeExists ? 'disabled-card' : '' }} block bg-white rounded-2xl border border-slate-200 overflow-hidden group"
               style="animation-delay: {{ $loop->index * 70 }}ms">

                <div class="px-6 pt-6 pb-5">

                    {{-- Icon + badge row --}}
                    <div class="flex items-start justify-between mb-5">
                        <div class="card-icon-wrap w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-{{ $mod['icon'] }} text-slate-500 text-lg"></i>
                        </div>
                        @if(!$routeExists)
                            <span class="text-[10px] font-black bg-slate-100 text-slate-400 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                {{ __('app.coming_soon') }}
                            </span>
                        @else
                            <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full mt-1.5 shadow shadow-emerald-200 flex-shrink-0"></span>
                        @endif
                    </div>

                    {{-- Name + description --}}
                    <h3 class="text-base font-black text-slate-800 mb-1.5 group-hover:text-indigo-700 transition-colors">
                        {{ $modName }}
                    </h3>
                    <p class="text-sm text-slate-400 leading-relaxed">{{ $modDesc }}</p>
                </div>

                {{-- Footer --}}
                <div class="border-t border-slate-100 px-6 py-3.5 flex items-center justify-between bg-slate-50/50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        {{ $routeExists ? __('app.open_module') : __('app.coming_soon') }}
                    </span>
                    @if($routeExists)
                        <i class="fa-solid fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-slate-300 text-sm card-arrow"></i>
                    @else
                        <i class="fa-solid fa-lock text-slate-300 text-sm"></i>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</main>

{{-- Flash messages --}}
@if(session('success') || session('error'))
<div class="fixed bottom-6 end-6 z-50 flex flex-col gap-3">
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-500 text-white px-5 py-3.5 rounded-2xl shadow-xl max-w-sm"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 translate-y-4">
            <i class="fa-solid fa-circle-check text-xl flex-shrink-0"></i>
            <p class="font-semibold text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-rose-500 text-white px-5 py-3.5 rounded-2xl shadow-xl max-w-sm"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 translate-y-4">
            <i class="fa-solid fa-circle-xmark text-xl flex-shrink-0"></i>
            <p class="font-semibold text-sm">{{ session('error') }}</p>
        </div>
    @endif
</div>
@endif

<script>
    const greetings = {
        morning:   '{{ __('app.greeting_morning') }}',
        afternoon: '{{ __('app.greeting_afternoon') }}',
        evening:   '{{ __('app.greeting_evening') }}',
        night:     '{{ __('app.greeting_night') }}',
    };
    const isRtl = {{ $isRtl ? 'true' : 'false' }};

    function updateClock() {
        const now    = new Date();
        const locale = isRtl ? 'ar-SA' : 'en-US';
        const timeEl = document.getElementById('hdr-time');
        const dateEl = document.getElementById('hdr-date');
        const greetEl= document.getElementById('greeting');

        if (timeEl) timeEl.textContent = now.toLocaleTimeString(locale, { hour: '2-digit', minute: '2-digit' });
        if (dateEl) dateEl.textContent = now.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long' });

        if (greetEl) {
            const h = now.getHours();
            greetEl.textContent = h < 5 ? greetings.night
                                : h < 12 ? greetings.morning
                                : h < 17 ? greetings.afternoon
                                : h < 21 ? greetings.evening
                                : greetings.night;
        }
    }
    updateClock();
    setInterval(updateClock, 30000);
</script>
</body>
</html>
