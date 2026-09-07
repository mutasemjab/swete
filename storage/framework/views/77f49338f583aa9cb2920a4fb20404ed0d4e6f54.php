<?php
    $isRtl   = app()->isLocale('ar');
    $dir     = $isRtl ? 'rtl' : 'ltr';
    $lang    = app()->getLocale();
    $modName = __($currentModuleConfig['name']);
?>
<!DOCTYPE html>
<html lang="<?php echo e($lang); ?>" dir="<?php echo e($dir); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', $modName); ?> | ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <style type="text/tailwindcss">
        * { font-family: 'Cairo', sans-serif; }

        .sidebar-link {
            @apply relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 cursor-pointer select-none;
        }
        .sidebar-link:hover { @apply bg-slate-50 text-slate-900; }
        .sidebar-link.active { @apply font-bold; }

        .card        { @apply bg-white rounded-2xl shadow-sm border border-slate-100; }
        .card-header { @apply px-6 py-4 border-b border-slate-100 flex items-center justify-between; }

        .btn          { @apply inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed; }
        .btn-primary  { @apply btn bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm hover:shadow-lg hover:shadow-indigo-200/60; }
        .btn-secondary{ @apply btn bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:border-slate-300; }
        .btn-danger   { @apply btn bg-rose-600 text-white hover:bg-rose-700 shadow-sm hover:shadow-lg hover:shadow-rose-200/60; }
        .btn-sm       { @apply !px-3 !py-1.5 !text-xs !rounded-lg; }

        .form-label  { @apply block text-sm font-semibold text-slate-700 mb-1.5; }
        .form-input  { @apply w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 placeholder:text-slate-400 transition-all duration-200; }
        .form-input.is-invalid { @apply border-rose-400 focus:ring-rose-500/20 focus:border-rose-500; }
        .form-select { @apply form-input cursor-pointer; }
        .form-error  { @apply flex items-center gap-1.5 text-rose-500 text-xs mt-1.5 font-medium; }

        .badge { @apply inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold; }

        .page-header   { @apply flex items-start justify-between mb-6; }
        .page-title    { @apply text-2xl font-black text-slate-800 leading-tight; }
        .page-subtitle { @apply text-slate-500 text-sm mt-1; }
    </style>

    
    <style>
        .select2-container--default .select2-selection--single {
            height: 44px; border: 1px solid #e2e8f0; border-radius: 0.75rem;
            display: flex; align-items: center; padding: 0 0.75rem;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #818cf8; box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2);
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding: 0; font-size: 0.875rem; color: #1e293b; line-height: 1.25rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #94a3b8; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; }
        [dir="rtl"] .select2-container--default .select2-selection--single .select2-selection__arrow { left: 0.5rem; right: auto; }
        .select2-dropdown { border-radius: 0.75rem; border-color: #e2e8f0; overflow: hidden; }
        .select2-search--dropdown .select2-search__field {
            border-radius: 0.5rem; border-color: #e2e8f0; padding: 0.4rem 0.6rem; outline: none;
        }
        .select2-results__option--highlighted[aria-selected] { background-color: #4f46e5 !important; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-slate-50 text-slate-800 antialiased"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

<div class="flex min-h-screen">

    

    
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-20 lg:hidden">
    </div>

    
    <aside class="fixed inset-y-0 start-0 z-30 flex flex-col w-64 bg-white border-e border-slate-100 shadow-2xl shadow-slate-900/5
                  transition-transform duration-300 ease-out lg:shadow-none lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '<?php echo e($isRtl ? '-translate-x-full' : 'translate-x-full'); ?>'">

        
        <div class="relative overflow-hidden bg-gradient-to-br <?php echo e($currentModuleConfig['gradient']); ?> px-5 py-5 flex-shrink-0">
            <div class="absolute -top-8 -end-8 w-28 h-28 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute top-4 end-4 w-14 h-14 bg-white/5 rounded-full pointer-events-none"></div>

            <a href="<?php echo e(route('dashboard')); ?>"
               class="relative flex items-center gap-2 text-white/70 hover:text-white text-xs font-semibold mb-5 transition-colors w-fit group">
                <i class="fa-solid fa-grid-2 text-xs group-hover:scale-110 transition-transform"></i>
                <span><?php echo e(__('app.dashboard')); ?></span>
            </a>

            <div class="relative flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-inner flex-shrink-0">
                    <i class="fa-solid fa-<?php echo e($currentModuleConfig['icon']); ?> text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-white font-black text-base leading-tight"><?php echo e($modName); ?></h2>
                    <p class="text-white/50 text-[11px] mt-0.5 font-medium"><?php echo e(__('app.current_section')); ?></p>
                </div>
            </div>
        </div>

        
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1"
             style="scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">
            <?php $__currentLoopData = $currentModuleConfig['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div x-data="{ open: true }">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-[11px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 hover:bg-slate-50 transition-all">
                        <span><?php echo e(__($section['label'])); ?></span>
                        <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-300"
                           :class="{ 'rotate-180': !open }"></i>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0"
                         class="mt-0.5 space-y-0.5">
                        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $routeExists = $item['route'] !== '#' && \Illuminate\Support\Facades\Route::has($item['route']);
                                $color       = $currentModuleConfig['color'];
                                $params      = $item['params'] ?? [];
                                // Index items stay active for show/edit but not create; other items exact-match
                                if ($routeExists && str_ends_with($item['route'], '.index')) {
                                    $base     = substr($item['route'], 0, -6);
                                    $isActive = request()->routeIs($base . '.*') && !request()->routeIs($base . '.create');
                                } else {
                                    $isActive = $routeExists && request()->routeIs($item['route']);
                                }
                                // Nav items that share one route name but differ by a route param
                                // (e.g. the 3 voucher types) only stay active for their own param value.
                                foreach ($params as $paramKey => $paramValue) {
                                    $isActive = $isActive && (string) request()->route($paramKey) === (string) $paramValue;
                                }
                            ?>

                            <?php if($routeExists): ?>
                                <a href="<?php echo e(route($item['route'], $params)); ?>"
                                   class="sidebar-link <?php echo e($isActive
                                        ? "active bg-{$color}-50 text-{$color}-700"
                                        : 'text-slate-600'); ?>">
                                    <?php if($isActive): ?>
                                        <span class="absolute start-0 inset-y-2 w-1 bg-<?php echo e($color); ?>-500 rounded-e-full"></span>
                                    <?php endif; ?>
                                    <i class="fa-solid fa-<?php echo e($item['icon']); ?> w-4 text-center text-sm
                                              <?php echo e($isActive ? "text-{$color}-600" : 'text-slate-400'); ?>"></i>
                                    <span><?php echo e(__($item['label'])); ?></span>
                                </a>
                            <?php else: ?>
                                <div class="sidebar-link text-slate-400 !cursor-not-allowed">
                                    <i class="fa-solid fa-<?php echo e($item['icon']); ?> w-4 text-center text-sm text-slate-300"></i>
                                    <span class="flex-1 text-slate-400"><?php echo e(__($item['label'])); ?></span>
                                    <span class="text-[9px] font-black bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-md uppercase tracking-wider"><?php echo e(__('app.coming_soon')); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        
        <div class="flex-shrink-0 border-t border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br <?php echo e(Auth::user()->avatar_gradient); ?>

                            flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                    <?php echo e(Auth::user()->initials); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate"><?php echo e(Auth::user()->name); ?></p>
                    <p class="text-xs text-slate-400 truncate"><?php echo e(Auth::user()->email); ?></p>
                </div>
                <form action="<?php echo e(route('auth.logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            title="<?php echo e(__('app.logout')); ?>"
                            class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                        <i class="fa-solid fa-right-from-bracket text-sm <?php echo e($isRtl ? 'fa-flip-horizontal' : ''); ?>"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    
    <div class="flex-1 flex flex-col min-h-screen lg:ps-64 transition-all duration-300">

        
        <header class="sticky top-0 z-10 h-16 bg-white/95 backdrop-blur border-b border-slate-100
                       flex items-center px-5 gap-4 shadow-sm">

            
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 rounded-xl hover:bg-slate-100 text-slate-600 transition-colors flex-shrink-0">
                <i class="fa-solid fa-bars"></i>
            </button>

            
            <nav class="flex items-center gap-2 text-sm flex-1 min-w-0" aria-label="breadcrumb">
                <a href="<?php echo e(route('dashboard')); ?>"
                   class="text-slate-400 hover:text-indigo-600 transition-colors flex items-center gap-1.5 flex-shrink-0">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span class="hidden sm:inline"><?php echo e(__('app.home')); ?></span>
                </a>
                <i class="fa-solid fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-slate-300 text-[10px] flex-shrink-0"></i>
                <?php
                    $modRoute     = $currentModuleConfig['route'];
                    $modRouteHref = ($modRoute !== '#' && \Illuminate\Support\Facades\Route::has($modRoute))
                        ? route($modRoute) : '#';
                ?>
                <a href="<?php echo e($modRouteHref); ?>"
                   class="text-slate-500 hover:text-slate-800 transition-colors truncate">
                    <?php echo e($modName); ?>

                </a>
                <?php if (! empty(trim($__env->yieldContent('breadcrumb')))): ?>
                    <i class="fa-solid fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-slate-300 text-[10px] flex-shrink-0"></i>
                    <span class="text-slate-800 font-bold truncate"><?php echo $__env->yieldContent('breadcrumb'); ?></span>
                <?php endif; ?>
            </nav>

            
            <div class="flex items-center gap-1.5 flex-shrink-0">

                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-600 transition-colors text-sm font-semibold">
                        <i class="fa-solid fa-globe text-xs"></i>
                        <span class="hidden sm:inline"><?php echo e($isRtl ? __('app.arabic') : __('app.english')); ?></span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"
                           :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute end-0 top-full mt-2 w-40 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-100 py-2 z-50">
                        <a href="<?php echo e(route('lang.switch', 'ar')); ?>"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-slate-50
                                  <?php echo e($isRtl ? 'text-indigo-600 font-bold' : 'text-slate-700'); ?>">
                            <?php if($isRtl): ?><i class="fa-solid fa-check text-xs text-indigo-500"></i><?php else: ?><span class="w-4"></span><?php endif; ?>
                            العربية
                        </a>
                        <a href="<?php echo e(route('lang.switch', 'en')); ?>"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-slate-50
                                  <?php echo e(!$isRtl ? 'text-indigo-600 font-bold' : 'text-slate-700'); ?>">
                            <?php if(!$isRtl): ?><i class="fa-solid fa-check text-xs text-indigo-500"></i><?php else: ?><span class="w-4"></span><?php endif; ?>
                            English
                        </a>
                    </div>
                </div>

                
                <a href="<?php echo e(route('approvals.index')); ?>" title="<?php echo e(__('approvals.my_approvals')); ?>"
                   class="relative p-2 rounded-xl hover:bg-slate-100 text-slate-500 transition-colors">
                    <i class="fa-solid fa-clipboard-check text-sm"></i>
                    <?php if($pendingApprovalsCount > 0): ?>
                        <span class="absolute -top-0.5 -end-0.5 min-w-[16px] h-4 px-1 flex items-center justify-center bg-rose-500 text-white text-[9px] font-black rounded-full border-2 border-white">
                            <?php echo e($pendingApprovalsCount > 9 ? '9+' : $pendingApprovalsCount); ?>

                        </span>
                    <?php endif; ?>
                </a>

                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl hover:bg-slate-50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br <?php echo e(Auth::user()->avatar_gradient); ?>

                                    flex items-center justify-center text-white text-xs font-black">
                            <?php echo e(Auth::user()->initials); ?>

                        </div>
                        <div class="hidden md:block text-start leading-tight">
                            <p class="text-sm font-bold text-slate-700"><?php echo e(Auth::user()->name); ?></p>
                            <p class="text-[11px] text-slate-400"><?php echo e(Auth::user()->role_label); ?></p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 hidden md:block transition-transform duration-200"
                           :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute end-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-100 py-2 z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-bold text-slate-800"><?php echo e(Auth::user()->name); ?></p>
                            <p class="text-xs text-slate-400 mt-0.5 truncate" dir="ltr"><?php echo e(Auth::user()->email); ?></p>
                        </div>
                        <a href="<?php echo e(route('settings.users.show', Auth::id())); ?>"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fa-solid fa-user w-4 text-center text-slate-400"></i>
                            <?php echo e(__('app.profile')); ?>

                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <form action="<?php echo e(route('auth.logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center <?php echo e($isRtl ? 'fa-flip-horizontal' : ''); ?>"></i>
                                <?php echo e(__('app.logout')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        
        <main class="flex-1 p-6">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>


<div class="fixed bottom-6 end-6 z-50 flex flex-col gap-3 pointer-events-none">
    <?php if(session('success')): ?>
        <div class="pointer-events-auto flex items-center gap-3 bg-emerald-500 text-white
                    px-5 py-3.5 rounded-2xl shadow-xl shadow-emerald-500/30 max-w-sm"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4">
            <i class="fa-solid fa-circle-check text-xl flex-shrink-0"></i>
            <p class="font-semibold text-sm"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="pointer-events-auto flex items-center gap-3 bg-rose-500 text-white
                    px-5 py-3.5 rounded-2xl shadow-xl shadow-rose-500/30 max-w-sm"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4">
            <i class="fa-solid fa-circle-xmark text-xl flex-shrink-0"></i>
            <p class="font-semibold text-sm"><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>
</div>


<div x-data="{ show: false, action: '', message: '' }"
     @delete-confirm.window="show = true; action = $event.detail.action; message = $event.detail.message ?? '<?php echo e(__('app.delete_confirm_msg')); ?>'">

    <div x-show="show"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">

        <div x-show="show"
             @click.outside="show = false"
             x-transition:enter="transition duration-200"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition duration-150"
             x-transition:leave-end="opacity-0 scale-90"
             class="bg-white rounded-3xl shadow-2xl shadow-slate-900/20 p-8 max-w-sm w-full text-center">

            <div class="w-16 h-16 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-800 mb-2"><?php echo e(__('app.confirm_delete')); ?></h3>
            <p class="text-slate-500 text-sm mb-7 leading-relaxed" x-text="message"></p>

            <div class="flex gap-3">
                <button @click="show = false"
                        class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors">
                    <?php echo e(__('app.cancel')); ?>

                </button>
                <form :action="action" method="POST" class="flex-1">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 transition-colors">
                        <?php echo e(__('app.yes_delete')); ?>

                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    window.initSelect2 = function (context) {
        (context ? $(context) : $(document)).find('.js-select2').each(function () {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) return;
            $el.select2({
                dir: '<?php echo e($isRtl ? "rtl" : "ltr"); ?>',
                width: '100%',
                placeholder: $el.data('placeholder') || $el.find('option[value=""]').first().text() || '',
                allowClear: $el.find('option[value=""]').length > 0 && !$el.prop('required'),
            });
        });
    };
    document.addEventListener('DOMContentLoaded', () => window.initSelect2());
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/layouts/app.blade.php ENDPATH**/ ?>