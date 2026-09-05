<?php
    $styles = [
        'pending'   => 'bg-amber-100 text-amber-700',
        'approved'  => 'bg-emerald-100 text-emerald-700',
        'rejected'  => 'bg-rose-100 text-rose-700',
        'cancelled' => 'bg-slate-100 text-slate-500',
    ];
?>
<span class="badge <?php echo e($styles[$status] ?? $styles['cancelled']); ?>">
    <?php echo e(__('approvals.status_' . $status)); ?>

</span>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/components/approval-badge.blade.php ENDPATH**/ ?>