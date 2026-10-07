<?php
    $colors = [
        'open' => 'slate',
        'submitted_to_customer' => 'amber',
        'customer_signed' => 'violet',
        'closed' => 'emerald',
    ];
    $color = $colors[$status] ?? 'slate';
?>
<span class="badge bg-<?php echo e($color); ?>-100 text-<?php echo e($color); ?>-700"><?php echo e(__('maintenance.visit_status_' . $status)); ?></span>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/maintenance/visits/_status-badge.blade.php ENDPATH**/ ?>