<?php $isRtl = app()->isLocale('ar'); $numbers = $purchaseRequests->pluck('number')->implode(', '); ?>
<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo e(__('external_purchases.ship_email_subject', ['number' => $numbers])); ?></title>
</head>
<body style="font-family: Tahoma, Arial, sans-serif; color:#1a1a1a; font-size:14px; line-height:1.7;">
    <p><?php echo e(__('external_purchases.ship_email_greeting', ['company' => $shippingCompany->localized_name])); ?></p>

    <p><?php echo e(__('external_purchases.ship_email_intro', ['number' => $numbers])); ?></p>

    <?php if($purchaseRequests->count() > 1): ?>
        <ul>
            <?php $__currentLoopData = $purchaseRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($pr->number); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>

    <?php if($note): ?>
        <p style="white-space: pre-line;"><?php echo e($note); ?></p>
    <?php endif; ?>

    <?php if(count($files)): ?>
        <p><strong><?php echo e(__('external_purchases.ship_email_attachments')); ?>:</strong></p>
        <ul>
            <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($file->getClientOriginalName()); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>

    <p><?php echo e(__('external_purchases.ship_email_signoff')); ?></p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/emails/shipping-quote-request.blade.php ENDPATH**/ ?>