<?php $__env->startSection('title', __('customer_portal.visit_review_title')); ?>

<?php $__env->startSection('bar'); ?>
<div class="cp-bar">
    <a href="<?php echo e(route('customer-portal.dashboard')); ?>" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-arrow-right-to-bracket fa-flip-horizontal"></i>
    </a>
    <div class="flex-1">
        <p class="font-black leading-tight"><?php echo e(__('customer_portal.visit_review_title')); ?></p>
        <p class="text-xs text-white/60" dir="ltr"><?php echo e($visit->check_in_at->format('Y-m-d')); ?></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php $__currentLoopData = $visit->reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="cp-card">
    <p class="font-black text-slate-800 mb-3"><?php echo e($report->template_name); ?></p>

    <?php if($report->problem): ?>
        <p class="text-sm text-slate-500 mb-1"><?php echo e(__('maintenance.report_problem')); ?></p>
        <p class="text-sm text-slate-700 mb-3"><?php echo e($report->problem); ?></p>
    <?php endif; ?>
    <?php if($report->solution): ?>
        <p class="text-sm text-slate-500 mb-1"><?php echo e(__('maintenance.report_solution')); ?></p>
        <p class="text-sm text-slate-700 mb-3"><?php echo e($report->solution); ?></p>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <?php $__currentLoopData = $report->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <p class="text-xs text-slate-400 mb-0.5"><?php echo e($field->localized_question); ?></p>
                <?php if($field->type === 'images'): ?>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <?php $__empty_1 = true; $__currentLoopData = $field->image_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($url); ?>" target="_blank"><img src="<?php echo e($url); ?>" class="w-16 h-16 object-cover rounded-lg border border-slate-200"></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="text-slate-400 text-sm">—</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="font-semibold text-slate-700 text-sm"><?php echo e($field->formatted_answer ?? '—'); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($report->materials->isNotEmpty()): ?>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-400 mb-1"><?php echo e(__('maintenance.report_materials_used')); ?></p>
            <?php $__currentLoopData = $report->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="text-sm text-slate-700"><?php echo e($material->material?->localized_name); ?> × <span dir="ltr"><?php echo e($material->quantity); ?></span></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($visit->isSubmitted()): ?>
<div class="cp-card" x-data="{
        drawing: false,
        hasDrawing: false,
        ctx: null,
        init() {
            this.ctx = this.$refs.canvas.getContext('2d');
            this.ctx.strokeStyle = '#1e293b';
            this.ctx.lineWidth = 2.5;
            this.ctx.lineCap = 'round';
        },
        point(e) {
            const rect = this.$refs.canvas.getBoundingClientRect();
            const x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
            const y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
            return { x, y };
        },
        start(e) {
            this.drawing = true;
            this.hasDrawing = true;
            const p = this.point(e);
            this.ctx.beginPath();
            this.ctx.moveTo(p.x, p.y);
        },
        move(e) {
            if (! this.drawing) return;
            const p = this.point(e);
            this.ctx.lineTo(p.x, p.y);
            this.ctx.stroke();
        },
        end() { this.drawing = false; },
        clear() {
            this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
            this.hasDrawing = false;
        },
        submit() {
            if (! this.hasDrawing) return;
            this.$refs.canvas.toBlob(blob => {
                const fd = new FormData();
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                fd.append('signature', new File([blob], 'signature.png', { type: 'image/png' }));
                fetch('<?php echo e(route('customer-portal.visits.sign', $visit)); ?>', { method: 'POST', body: fd })
                    .then(() => window.location.reload());
            });
        },
     }" x-init="init()">
    <p class="font-black text-slate-800 mb-2"><?php echo e(__('customer_portal.sign_here')); ?></p>
    <p class="text-xs text-slate-400 mb-3"><?php echo e(__('customer_portal.sign_hint')); ?></p>
    <canvas x-ref="canvas" width="600" height="200" class="w-full border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 touch-none"
            @mousedown="start($event)" @mousemove="move($event)" @mouseup="end()" @mouseleave="end()"
            @touchstart.prevent="start($event)" @touchmove.prevent="move($event)" @touchend.prevent="end()"></canvas>
    <div class="flex items-center gap-2 mt-3">
        <button type="button" @click="clear()" class="cp-btn-outline flex-1 justify-center"><?php echo e(__('customer_portal.clear_signature')); ?></button>
        <button type="button" @click="submit()" :disabled="!hasDrawing" class="cp-btn-primary flex-1 justify-center">
            <i class="fa-solid fa-check"></i>
            <?php echo e(__('customer_portal.confirm_and_sign')); ?>

        </button>
    </div>
</div>
<?php elseif($visit->signature_path): ?>
<div class="cp-card">
    <p class="font-black text-slate-800 mb-2"><?php echo e(__('customer_portal.your_signature')); ?></p>
    <img src="<?php echo e($visit->signature_url); ?>" class="h-20 border border-slate-200 rounded-lg bg-white">
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer-portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\swete\resources\views/customer-portal/visits/show.blade.php ENDPATH**/ ?>