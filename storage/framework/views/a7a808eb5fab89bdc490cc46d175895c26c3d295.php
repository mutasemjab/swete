<?php
    $isRtl = app()->isLocale('ar');
    $dir   = $isRtl ? 'rtl' : 'ltr';
    $lang  = app()->getLocale();
?>
<!DOCTYPE html>
<html lang="<?php echo e($lang); ?>" dir="<?php echo e($dir); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#0f172a">
    <title><?php echo e(__('maintenance.mobile_visit_title')); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <?php echo $__env->make('maintenance.visits._mobile-styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="min-h-screen pb-32">

    <div class="app-bar">
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="flex-1">
            <p class="font-black leading-tight"><?php echo e(__('maintenance.mobile_visit_title')); ?></p>
            <p class="text-xs text-white/60"><?php echo e(config('app.name', 'ERP')); ?></p>
        </div>
        <form method="POST" action="<?php echo e(route('auth.logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 active:bg-white/20" title="<?php echo e(__('app.logout')); ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        </form>
    </div>

    <div class="max-w-lg mx-auto px-4 pt-4"
         x-data="{
            customerId: '',
            availableRequests: <?php echo e($availableRequests->toJson()); ?>,
            requestId: '',
            get myRequests() { return this.availableRequests[this.customerId] || []; },
         }"
         x-init="$nextTick(() => window.initSelect2()); $watch('customerId', () => { requestId = ''; $nextTick(() => window.initSelect2()); });">

        <?php if(session('success')): ?>
            <div class="m-card !bg-emerald-50 !border-emerald-200 text-center py-6 mb-4">
                <p class="font-bold text-emerald-800"><?php echo e(session('success')); ?></p>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('maintenance-visits.mobile.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="m-card">
                <div class="flex items-center gap-2 mb-3">
                    <span class="m-chip bg-indigo-600 text-white">1</span>
                    <p class="font-black text-slate-800"><?php echo e(__('maintenance.mobile_choose_customer_step')); ?></p>
                </div>
                <select name="customer_id" x-model="customerId" class="js-select2 m-input" required>
                    <option value=""><?php echo e(__('app.select')); ?></option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->localized_name); ?> (<?php echo e($customer->code); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-500 text-xs mt-2 font-bold"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="m-card">
                <div class="flex items-center gap-2 mb-3">
                    <span class="m-chip bg-indigo-600 text-white">2</span>
                    <p class="font-black text-slate-800"><?php echo e(__('maintenance.visit_check_in_at')); ?></p>
                </div>
                <input type="datetime-local" name="check_in_at" value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>" class="m-input" dir="ltr" required>
                <p class="text-[11px] text-slate-400 mt-1.5"><?php echo e(__('maintenance.visit_check_in_hint')); ?></p>
            </div>

            <template x-if="myRequests.length > 0">
                <div class="m-card" x-cloak>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="m-chip bg-indigo-600 text-white">3</span>
                        <p class="font-black text-slate-800"><?php echo e(__('maintenance.visit_link_request')); ?></p>
                    </div>
                    <select name="maintenance_request_id" x-model="requestId" class="js-select2 m-input">
                        <option value=""><?php echo e(__('maintenance.visit_no_linked_request')); ?></option>
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
                        <?php echo e(__('maintenance.visit_start')); ?>

                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php echo $__env->make('maintenance.visits._mobile-select2-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/visits/mobile-create.blade.php ENDPATH**/ ?>