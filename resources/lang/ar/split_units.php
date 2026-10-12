<?php

return [

    // ── Module ───────────────────────────────────────────────────────────────
    'module_name' => 'وحدات السبليت',
    'module_desc' => 'إدارة عروض أسعار وحدات التكييف السبليت',

    // ── Sidebar sections ─────────────────────────────────────────────────────
    'section_quotes' => 'وحدات السبليت',

    // ── Sidebar items ────────────────────────────────────────────────────────
    'nav_list' => 'قائمة وحدات السبليت',
    'nav_add'  => 'وحدة جديدة',

    // ── Index ────────────────────────────────────────────────────────────────
    'split_units' => 'وحدات السبليت',
    'list'        => 'قائمة وحدات السبليت',
    'subtitle'    => 'عروض أسعار وحدات التكييف السبليت',
    'add'         => 'إضافة وحدة',
    'add_subtitle'=> 'اختر العميل وأضف بنود عرض سعر الوحدة',
    'edit'        => 'تعديل وحدة سبليت',
    'total'       => 'إجمالي الوحدات',
    'no_quotes'   => 'لا توجد وحدات سبليت بعد',

    // ── Fields ───────────────────────────────────────────────────────────────
    'split_unit'    => 'وحدة سبليت',
    'number'        => 'رقم الوحدة',
    'date'          => 'التاريخ',
    'validity_weeks'=> 'مدة صلاحية العرض (أسابيع)',
    'assigned_to'   => 'مُرسل إلى',
    'unassigned'    => 'لم تُرسل بعد',

    // ── Status badges ────────────────────────────────────────────────────────
    'status_invoiced'        => 'تم تحويلها لفاتورة',
    'status_pending_invoice' => 'بانتظار التحويل',

    // ── Filters ──────────────────────────────────────────────────────────────
    'all_employees' => 'جميع الموظفين',

    // ── Items ────────────────────────────────────────────────────────────────
    'items'               => 'بنود الوحدة',
    'item_notes'          => 'ملاحظات البند',
    'item_note_placeholder' => 'ملاحظة',
    'add_item_note'       => 'إضافة ملاحظة',

    // ── Customer history panel ────────────────────────────────────────────────
    'customer_history'        => 'وحدات سابقة لهذا العميل',
    'customer_history_empty'  => 'لا توجد وحدات سابقة لهذا العميل.',

    // ── Flash messages ────────────────────────────────────────────────────────
    'quote_added'            => 'تم إنشاء وحدة السبليت بنجاح.',
    'quote_updated'          => 'تم تحديث وحدة السبليت بنجاح.',
    'quote_deleted'          => 'تم حذف وحدة السبليت بنجاح.',
    'quote_discount_exceeds' => 'لا يمكن أن يزيد الخصم عن المجموع.',

    // ── Very Important Notes (print) — mirrors tenders.quote_note_* ──────────
    'and'                            => 'و',
    'quote_note_validity'            => 'صلاحية العرض: :weeks أسابيع من تاريخ العرض.',
    'quote_note_technical_submittal' => 'جميع المعلومات الفنية حسب الـ Submittal المعتمد لدينا.',
    'quote_note_supply_scope'        => 'الأسعار شاملة :scope.',
    'quote_note_winching_included'   => 'السعر شامل أعمال الرفع (Winching).',
    'quote_note_winching_excluded'   => 'السعر غير شامل أعمال الرفع (Winching).',
    'quote_note_delivery_term'       => 'السعر شامل :term.',
    'quote_item_sales_tax'           => 'ضريبة المبيعات',
    'quote_item_customs_fees'        => 'الرسوم الجمركية',
    'quote_note_prices_include'      => 'الأسعار شاملة :items.',
    'quote_note_prices_exclude'      => 'الأسعار غير شاملة :items.',
    'quote_note_boiler'              => 'يرجى العلم أنه في بعض الحالات قد تحتاج الغلاية (Boiler) للعمل في حال وجود حاجة لرفع درجة حرارة مياه الخزان فوق 55°C أو خلال فترات الطلب العالي على المياه الساخنة.',
    'quote_note_currency'            => 'الأسعار بعملة :code.',

    // ── Print document ────────────────────────────────────────────────────────
    'print_prepared_by'          => 'مُقدَّم من / أُعِد بواسطة',
    'print_number'               => 'رقم الوحدة',
    'print_full_price'           => 'السعر الكامل',
    'print_very_important_notes' => 'ملاحظات هامة جدًا',

];
