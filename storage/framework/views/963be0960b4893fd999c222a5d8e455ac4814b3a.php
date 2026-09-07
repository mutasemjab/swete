<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->isLocale('ar') ? 'rtl' : 'ltr'); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo e($purchaseRequest->number); ?></title>
<style>
    :root { --po-red: #a4182a; --po-border: #2b2b2b; }

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

    .po-page {
        width: 210mm;
        min-height: 297mm;
        margin: 10mm auto;
        padding: 14mm 12mm;
        background: #fff;
        box-shadow: 0 0 8px rgba(0,0,0,.15);
    }

    .po-toolbar {
        max-width: 210mm;
        margin: 0 auto 10px;
        text-align: center;
    }

    .po-toolbar button {
        background: var(--po-red);
        color: #fff;
        border: none;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
    }

    .po-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .po-logo-slot {
        width: 150px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .po-logo-slot img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .po-logo-placeholder {
        width: 100%;
        height: 100%;
        border: 1px dashed #b5b5b5;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a3a3a3;
        font-size: 10px;
    }

    .po-title-block { flex: 1; text-align: center; }
    .po-title { font-size: 18px; font-weight: 800; color: var(--po-red); margin: 0 0 4px; }
    .po-subtitle { font-size: 11px; color: #555; margin: 0; }

    .po-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3px 24px;
        margin-bottom: 14px;
        font-size: 12px;
    }

    .po-meta span.label { color: #555; }
    .po-meta span.value { font-weight: 700; }

    .po-boxes {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }

    .po-box { flex: 1; border: 1px solid var(--po-border); }
    .po-box-header {
        background: var(--po-red);
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 9px;
    }
    .po-box-body { padding: 8px 9px; line-height: 1.65; min-height: 70px; }
    .po-box-body strong { display: block; margin-bottom: 2px; }

    table.po-items { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.po-items th, table.po-items td {
        border: 1px solid var(--po-border);
        padding: 6px 8px;
        font-size: 11px;
    }
    table.po-items th { background: var(--po-red); color: #fff; font-weight: 700; }
    table.po-items td.num { text-align: center; width: 34px; }
    table.po-items td.amount, table.po-items th.amount { text-align: end; width: 90px; }

    .po-bottom { display: flex; gap: 16px; margin-bottom: 30px; }
    .po-notes { flex: 1; }
    .po-notes-title { font-weight: 700; margin-bottom: 4px; }
    .po-notes-body { white-space: pre-line; color: #333; }

    .po-totals { width: 230px; flex-shrink: 0; }
    .po-totals table { width: 100%; border-collapse: collapse; }
    .po-totals td { padding: 5px 8px; font-size: 12px; }
    .po-totals tr.grand td { font-weight: 800; border-top: 2px solid var(--po-border); font-size: 13px; }
    .po-totals td.amount { text-align: end; }

    .po-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
        font-size: 12px;
    }
    .po-footer .line {
        display: inline-block;
        min-width: 160px;
        border-bottom: 1px solid #333;
        margin-inline-start: 8px;
    }

    @media print {
        body { background: #fff; }
        .po-toolbar { display: none !important; }
        .po-page { box-shadow: none; margin: 0; width: auto; min-height: 0; }
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>
</head>
<body>

<div class="po-toolbar">
    <button type="button" onclick="window.print()">
        <?php echo e(__('external_purchases.print')); ?>

    </button>
</div>

<div class="po-page">

    <div class="po-header">
        <div class="po-logo-slot">
            <?php if($purchaseRequest->branch?->logo_url): ?>
                <img src="<?php echo e($purchaseRequest->branch->logo_url); ?>" alt="">
            <?php else: ?>
                <div class="po-logo-placeholder"><?php echo e(__('external_purchases.logo_placeholder')); ?></div>
            <?php endif; ?>
        </div>

        <div class="po-title-block">
            <p class="po-title"><?php echo e(__('external_purchases.purchase_request')); ?></p>
            <p class="po-subtitle"><?php echo e($purchaseRequest->number); ?></p>
        </div>

        <div class="po-logo-slot">
            <?php if($purchaseRequest->branch?->logo_secondary_url): ?>
                <img src="<?php echo e($purchaseRequest->branch->logo_secondary_url); ?>" alt="">
            <?php else: ?>
                <div class="po-logo-placeholder"><?php echo e(__('external_purchases.logo_placeholder')); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="po-meta">
        <div><span class="label"><?php echo e(__('external_purchases.request_date')); ?>:</span> <span class="value"><?php echo e($purchaseRequest->date->format('M d, Y')); ?></span></div>
        <div><span class="label"><?php echo e(__('external_purchases.request_currency')); ?>:</span> <span class="value"><?php echo e($purchaseRequest->currency?->localized_name ?? '—'); ?></span></div>
        <?php if($purchaseRequest->project): ?>
            <div><span class="label"><?php echo e(__('external_purchases.po_project')); ?>:</span> <span class="value"><?php echo e($purchaseRequest->project->number); ?></span></div>
            <div><span class="label"><?php echo e(__('tenders.project_title')); ?>:</span> <span class="value"><?php echo e($purchaseRequest->project->localized_title); ?></span></div>
        <?php endif; ?>
    </div>

    <div class="po-boxes">
        <div class="po-box">
            <div class="po-box-header"><?php echo e(__('external_purchases.request_supplier')); ?></div>
            <div class="po-box-body">
                <strong><?php echo e($purchaseRequest->supplier?->localized_name ?? '—'); ?></strong>
                <?php if($purchaseRequest->supplier?->address): ?><?php echo e($purchaseRequest->supplier->address); ?><br><?php endif; ?>
                <?php if($purchaseRequest->supplier?->phone): ?>Tel: <?php echo e($purchaseRequest->supplier->phone); ?><br><?php endif; ?>
                <?php if($purchaseRequest->supplier?->email): ?><?php echo e($purchaseRequest->supplier->email); ?><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="po-boxes">
        <div class="po-box">
            <div class="po-box-header"><?php echo e(__('external_purchases.po_invoice_address')); ?></div>
            <div class="po-box-body">
                <strong><?php echo e($purchaseRequest->branch?->localized_name ?? '—'); ?></strong>
                <?php $__empty_1 = true; $__currentLoopData = $purchaseRequest->request_address_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo e($line); ?><br>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php endif; ?>
                <?php if($purchaseRequest->branch?->phone): ?>Tel: <?php echo e($purchaseRequest->branch->phone); ?><?php endif; ?>
                <?php if($purchaseRequest->branch?->fax): ?> / Fax: <?php echo e($purchaseRequest->branch->fax); ?><?php endif; ?>
            </div>
        </div>

        <div class="po-box">
            <div class="po-box-header"><?php echo e(__('external_purchases.request_shipping_address')); ?></div>
            <div class="po-box-body">
                <?php $__empty_1 = true; $__currentLoopData = $purchaseRequest->shipping_address_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo e($line); ?><br>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    —
                <?php endif; ?>
            </div>
        </div>
    </div>

    <table class="po-items">
        <thead>
            <tr>
                <th class="num"><?php echo e(__('external_purchases.po_item_no')); ?></th>
                <th><?php echo e(__('external_purchases.po_item_description')); ?></th>
                <th class="amount"><?php echo e(__('warehouse.voucher_item_quantity')); ?></th>
                <th class="amount"><?php echo e(__('accounting.invoice_item_unit_price')); ?></th>
                <th class="amount"><?php echo e(__('accounting.invoice_item_total')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $purchaseRequest->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="po-bottom">
        <div class="po-notes" dir="ltr" style="text-align:left;">
            <p class="po-notes-title">Additional Notes:</p>
            <p class="po-notes-body">
                Incoterm: EXWork<br>
                Payment Term: 60 Days after Invoice date.<br>
                Language Of Documentation: English<br>
                All Documents Shall be sent to: Invoice Address
            </p>
            <?php if($purchaseRequest->notes): ?>
                <p class="po-notes-body"><?php echo e($purchaseRequest->notes); ?></p>
            <?php endif; ?>
        </div>

        <div class="po-totals">
            <table>
                <tr>
                    <td><?php echo e(__('external_purchases.po_subtotal')); ?></td>
                    <td class="amount"><?php echo e(number_format($purchaseRequest->subtotal, 3)); ?></td>
                </tr>
                <tr class="grand">
                    <td><?php echo e(__('external_purchases.request_total')); ?></td>
                    <td class="amount"><?php echo e(number_format($purchaseRequest->total, 3)); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="po-footer">
        <div><?php echo e(__('external_purchases.po_authorized_by')); ?>: <span class="line"></span></div>
        <div><?php echo e(__('external_purchases.po_signature')); ?>: <span class="line"></span></div>
    </div>

</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\swete\resources\views/external-purchases/purchase-requests/print.blade.php ENDPATH**/ ?>