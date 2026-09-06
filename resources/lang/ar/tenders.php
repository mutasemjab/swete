<?php

return [

    // ── Module ───────────────────────────────────────────────────────────────
    'module_name' => 'العطاءات',
    'module_desc' => 'إدارة العطاءات وعروض الأسعار وتتبع حالتها',

    // ── Sidebar sections ─────────────────────────────────────────────────────
    'section_management'    => 'العطاءات',
    'section_price_quotes'  => 'عروض الأسعار',
    'section_settings'      => 'إعدادات العطاءات',

    // ── Sidebar items ────────────────────────────────────────────────────────
    'nav_list'          => 'قائمة العطاءات',
    'nav_add'           => 'إضافة عطاء',
    'nav_quotes_list'   => 'قائمة عروض الأسعار',
    'nav_quotes_add'    => 'عرض سعر جديد',
    'nav_statuses'      => 'حالات العطاء',

    // ── Tenders ──────────────────────────────────────────────────────────────
    'tenders'               => 'العطاءات',
    'tender'                => 'عطاء',
    'tenders_list'          => 'قائمة العطاءات',
    'tenders_subtitle'      => 'عرض العطاءات وإضافة عطاء جديد',
    'add_tender'            => 'إضافة عطاء',
    'add_tender_subtitle'   => 'أدخل بيانات العطاء الجديد',
    'edit_tender'           => 'تعديل عطاء',
    'tender_number'         => 'رقم العطاء',
    'tender_title'          => 'عنوان العطاء',
    'tender_entity_name'    => 'الجهة المعلنة',
    'tender_customer'       => 'العميل',

    'tender_location_scope'   => 'موقع العطاء',
    'location_inside_jordan'  => 'داخل الأردن',
    'location_outside_jordan' => 'خارج الأردن',
    'tender_governorate'      => 'المحافظة',
    'tender_country'          => 'الدولة',

    'tender_tax_exempt'     => 'معفي من الضريبة',
    'tender_customs_exempt' => 'معفي من الجمارك',

    'tender_delivery_terms'   => 'مكان التسليم',
    'delivery_terms_site'     => 'الموقع',
    'delivery_terms_cfr'      => 'CFR',
    'delivery_terms_exwork'   => 'EX-Works',

    'tender_coverage'                        => 'نطاق العطاء',
    'coverage_supply'                        => 'توريد فقط',
    'coverage_supply_execution'              => 'توريد وتنفيذ',
    'coverage_design'                        => 'تصميم فقط',
    'coverage_design_execution'              => 'تصميم وتنفيذ',
    'coverage_design_supply_execution'       => 'تصميم وتوريد وتنفيذ',

    'tender_description'         => 'وصف العطاء',
    'tender_win_probability'     => 'فرصة الفوز بالعطاء',
    'tender_submission_deadline' => 'آخر موعد للتقديم',
    'tender_status'              => 'الحالة',
    'tender_documents_url'         => 'رابط أوراق العطاء',
    'tender_design_documents_url'  => 'رابط أوراق التصميم والـ Selection',
    'tender_notes'                => 'ملاحظات',

    'no_tenders'            => 'لا توجد عطاءات بعد',
    'no_tenders_search'     => 'لا توجد نتائج تطابق بحثك.',
    'add_first_tender'      => 'إضافة أول عطاء',
    'tender_added'          => 'تم إضافة العطاء بنجاح.',
    'tender_updated'        => 'تم تحديث العطاء بنجاح.',
    'tender_deleted'        => 'تم حذف العطاء بنجاح.',
    'total_tenders'         => 'إجمالي العطاءات',
    'all_statuses_tender'   => 'جميع الحالات',

    // ── Tender statuses (dynamic/manageable list) ────────────────────────────
    'status'              => 'حالة العطاء',
    'statuses_list'        => 'حالات العطاء',
    'statuses_subtitle'    => 'حالات قابلة للإدارة — أضف أي حالة جديدة تحتاجها',
    'add_status'           => 'إضافة حالة',
    'edit_status'          => 'تعديل حالة',
    'status_name'          => 'اسم الحالة',
    'status_code'          => 'الرمز',
    'status_color'         => 'اللون',
    'no_statuses'          => 'لا توجد حالات بعد',
    'status_added'         => 'تم إضافة الحالة بنجاح.',
    'status_updated'       => 'تم تحديث الحالة بنجاح.',
    'status_deleted'       => 'تم حذف الحالة بنجاح.',
    'status_in_use'        => 'لا يمكن حذف حالة مستخدمة بعطاءات موجودة.',

    // ── Price quotes ──────────────────────────────────────────────────────────
    'price_quotes'            => 'عروض الأسعار',
    'price_quote'             => 'عرض سعر',
    'price_quotes_list'       => 'قائمة عروض الأسعار',
    'price_quotes_subtitle'   => 'عروض الأسعار المرتبطة بالعملاء والعطاءات',
    'add_quote'               => 'عرض سعر جديد',
    'add_quote_subtitle'      => 'اختر العميل وأضف بنود عرض السعر',
    'quote_number'            => 'رقم عرض السعر',
    'quote_tender'            => 'العطاء',
    'quote_date'              => 'التاريخ',
    'quote_items'             => 'بنود عرض السعر',
    'quote_total'             => 'الإجمالي',
    'quote_added'             => 'تم إنشاء عرض السعر بنجاح.',
    'no_quotes'               => 'لا توجد عروض أسعار بعد',
    'total_quotes'            => 'إجمالي العروض',
    'attach_existing_quote'   => 'إرفاق عرض سعر موجود',
    'attach_quote'            => 'إرفاق',
    'quote_attached'          => 'تم إرفاق عرض السعر بالعطاء بنجاح.',

];
