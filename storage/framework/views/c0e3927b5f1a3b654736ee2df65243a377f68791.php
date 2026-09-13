<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->isLocale('ar') ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo e($priceQuote->number); ?></title>
<style>
    :root { --pq-red: #a4182a; --pq-border: #2b2b2b; }

    @page { size: A4; margin: 14mm 12mm; }

    * { box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        color: #1a1a1a;
        margin: 0;
        padding: 0;
        font-size: 12px;
        background: #f1f1f1;
    }

    .pq-page {
        width: 210mm;
        min-height: 297mm;
        margin: 10mm auto;
        padding: 14mm 12mm;
        background: #fff;
        box-shadow: 0 0 8px rgba(0,0,0,.15);
        display: flex;
        flex-direction: column;
    }

    .pq-toolbar {
        max-width: 210mm;
        margin: 0 auto 10px;
        text-align: center;
    }

    .pq-toolbar button {
        background: var(--pq-red);
        color: #fff;
        border: none;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
    }

    .pq-header-images {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-bottom: 20px;
    }
    .pq-header-images img { max-height: 60px; max-width: 150px; object-fit: contain; }

    .pq-prepared-by { text-align: center; margin-bottom: 6px; }
    .pq-prepared-by .label { font-size: 11px; color: #555; }
    .pq-prepared-by .value { font-size: 15px; font-weight: 800; color: var(--pq-red); }

    .pq-project-title { text-align: center; margin-bottom: 20px; }
    .pq-project-title .label { font-size: 11px; color: #555; }
    .pq-project-title .value { font-size: 15px; font-weight: 800; }

    .pq-body-images {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex: 1;
    }
    .pq-body-images img { max-width: 100%; max-height: 160px; object-fit: contain; }

    .pq-footer-address {
        text-align: center;
        font-size: 11px;
        color: #444;
        line-height: 1.7;
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 20px;
    }

    .pq-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin: 20px 0;
        font-size: 12px;
    }
    .pq-meta-row .label { color: #555; }
    .pq-meta-row .value { font-weight: 700; }

    table.pq-items { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    table.pq-items th, table.pq-items td {
        border: 1px solid var(--pq-border);
        padding: 6px 8px;
        font-size: 11px;
    }
    table.pq-items th { background: var(--pq-red); color: #fff; font-weight: 700; }
    table.pq-items td.num { text-align: center; width: 34px; }
    table.pq-items td.amount, table.pq-items th.amount { text-align: end; width: 90px; }

    .pq-notes-title { font-size: 13px; font-weight: 800; color: var(--pq-red); margin: 0 0 8px; }
    .pq-notes-list { margin: 0; padding-inline-start: 18px; font-size: 11.5px; line-height: 1.8; }

    @media print {
        body { background: #fff; }
        .pq-toolbar { display: none !important; }
        .pq-page { box-shadow: none; margin: 0; width: auto; min-height: 0; }
        .pq-page-2 { page-break-before: always; }
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>
</head>
<body>

<div class="pq-toolbar">
    <button type="button" onclick="window.print()">
        <?php echo e(__('external_purchases.print')); ?>

    </button>
</div>


<div class="pq-page">
    <div class="pq-header-images">
        <?php $__empty_1 = true; $__currentLoopData = $priceQuote->branch->quote_header_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <img src="<?php echo e($url); ?>" alt="">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
    </div>

    <div class="pq-prepared-by">
        <p class="label"><?php echo e(__('tenders.quote_print_prepared_by')); ?></p>
        <p class="value"><?php echo e($priceQuote->branch->localized_name); ?></p>
    </div>

    <div class="pq-project-title">
        <p class="label"><?php echo e(__('tenders.quote_print_project_title')); ?></p>
        <p class="value"><?php echo e($priceQuote->customer?->localized_name); ?></p>
    </div>

    <div class="pq-body-images">
        <?php $__empty_1 = true; $__currentLoopData = $priceQuote->branch->quote_body_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <img src="<?php echo e($url); ?>" alt="">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
    </div>

    <div class="pq-footer-address">
        <?php $__empty_1 = true; $__currentLoopData = $priceQuote->branch->localized_address_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <p style="margin:2px 0;"><?php echo e($line); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
    </div>
</div>


<div class="pq-page pq-page-2">
    <div class="pq-header-images">
        <?php $__empty_1 = true; $__currentLoopData = $priceQuote->branch->quote_header_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <img src="<?php echo e($url); ?>" alt="">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php endif; ?>
    </div>

    <div class="pq-meta-row">
        <div>
            <p><span class="label"><?php echo e(__('tenders.quote_print_subject')); ?>:</span> <span class="value"><?php echo e($priceQuote->tender?->localized_title ?? '—'); ?></span></p>
            <p><span class="label"><?php echo e(__('tenders.quote_print_project')); ?>:</span> <span class="value"><?php echo e($priceQuote->customer?->localized_name); ?></span></p>
        </div>
        <div style="text-align:end;">
            <p><span class="label"><?php echo e(__('tenders.quote_date')); ?>:</span> <span class="value"><?php echo e($priceQuote->date->format('M d, Y')); ?></span></p>
            <p><span class="label"><?php echo e(__('external_purchases.request_number')); ?>:</span> <span class="value"><?php echo e($priceQuote->number); ?></span></p>
        </div>
    </div>

    <table class="pq-items">
        <thead>
            <tr>
                <th class="num"><?php echo e(__('external_purchases.po_item_no')); ?></th>
                <th><?php echo e(__('external_purchases.po_item_description')); ?></th>
                <th class="amount"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                <th class="amount"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                <th class="amount"><?php echo e(__('tenders.quote_print_full_price')); ?></th>
                <th><?php echo e(__('tenders.quote_item_notes')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $priceQuote->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="num"><?php echo e($index + 1); ?></td>
                <td>
                    <?php echo e($item->material?->localized_name); ?>

                    <?php if($item->material?->unit?->symbol): ?>
                        <span style="color:#777;"> (<?php echo e($item->material->unit->symbol); ?>)</span>
                    <?php endif; ?>
                </td>
                <td class="amount"><?php echo e(number_format($item->quantity, 3)); ?></td>
                <td class="amount"><?php echo e(number_format($item->unit_price, 3)); ?></td>
                <td class="amount"><?php echo e(number_format($item->total, 3)); ?></td>
                <td><?php echo e($item->notes); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div>
        <p class="pq-notes-title"><?php echo e(__('tenders.quote_print_very_important_notes')); ?></p>
        <ul class="pq-notes-list">
            <?php $__currentLoopData = $priceQuote->very_important_notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($line); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/tenders/price-quotes/print.blade.php ENDPATH**/ ?>