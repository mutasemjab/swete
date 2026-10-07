<?php
    $isRtl = app()->isLocale('ar');
    $dir   = $isRtl ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e($dir); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', __('app.login_title')); ?> | <?php echo e(__('app.erp_name')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        * { font-family: 'Cairo', sans-serif; }

        .form-input {
            @apply w-full px-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-800 bg-white
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400
                   placeholder:text-slate-400 transition-all duration-200;
        }
        .form-input.is-invalid {
            @apply border-rose-400 bg-rose-50/30 focus:ring-rose-500/20 focus:border-rose-500;
        }
        .form-label {
            @apply block text-sm font-bold text-slate-700 mb-1.5;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4 relative overflow-hidden">

    
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -end-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -start-40 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-3xl"></div>
    </div>

    
    <div class="fixed top-4 end-4 z-10">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-1.5 bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-200
                           text-slate-600 text-sm font-semibold hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-globe text-xs"></i>
                <?php echo e($isRtl ? 'AR' : 'EN'); ?>

            </button>
            <div x-show="open"
                 @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute end-0 top-full mt-1.5 w-32 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                <a href="<?php echo e(route('lang.switch', 'ar')); ?>"
                   class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-slate-50 transition-colors
                          <?php echo e($isRtl ? 'text-indigo-600 font-bold' : 'text-slate-700'); ?>">العربية</a>
                <a href="<?php echo e(route('lang.switch', 'en')); ?>"
                   class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-slate-50 transition-colors
                          <?php echo e(!$isRtl ? 'text-indigo-600 font-bold' : 'text-slate-700'); ?>">English</a>
            </div>
        </div>
    </div>

    <div class="relative w-full max-w-md">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/layouts/auth.blade.php ENDPATH**/ ?>