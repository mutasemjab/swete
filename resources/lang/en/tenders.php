<?php

return [

    // ── Module ───────────────────────────────────────────────────────────────
    'module_name' => 'Tenders',
    'module_desc' => 'Manage tenders, price quotes, and track their status',

    // ── Sidebar sections ─────────────────────────────────────────────────────
    'section_management'    => 'Tenders',
    'section_price_quotes'  => 'Price Quotes',
    'section_settings'      => 'Tender Settings',

    // ── Sidebar items ────────────────────────────────────────────────────────
    'nav_list'          => 'Tenders List',
    'nav_add'           => 'Add Tender',
    'nav_quotes_list'   => 'Price Quotes List',
    'nav_quotes_add'    => 'New Price Quote',
    'nav_statuses'      => 'Tender Statuses',

    // ── Tenders ──────────────────────────────────────────────────────────────
    'tenders'               => 'Tenders',
    'tender'                => 'Tender',
    'tenders_list'          => 'Tenders List',
    'tenders_subtitle'      => 'View tenders and add a new one',
    'add_tender'            => 'Add Tender',
    'add_tender_subtitle'   => 'Enter the new tender details',
    'edit_tender'           => 'Edit Tender',
    'tender_number'         => 'Tender No.',
    'tender_title'          => 'Tender Title',
    'tender_entity_name'    => 'Issuing Entity',
    'tender_customer'       => 'Customer',

    'tender_location_scope'   => 'Tender Location',
    'location_inside_jordan'  => 'Inside Jordan',
    'location_outside_jordan' => 'Outside Jordan',
    'tender_governorate'      => 'Governorate',
    'tender_country'          => 'Country',

    'tender_tax_exempt'     => 'Tax Exempt',
    'tender_customs_exempt' => 'Customs Exempt',

    'tender_delivery_terms'   => 'Delivery Place',
    'delivery_terms_site'     => 'Site',
    'delivery_terms_cfr'      => 'CFR',
    'delivery_terms_exwork'   => 'EX-Works',

    'tender_coverage'                        => 'Tender Coverage',
    'coverage_supply'                        => 'Supply Only',
    'coverage_supply_execution'              => 'Supply & Execution',
    'coverage_design'                        => 'Design Only',
    'coverage_design_execution'              => 'Design & Execution',
    'coverage_design_supply_execution'       => 'Design, Supply & Execution',

    'tender_description'         => 'Tender Description',
    'tender_win_probability'     => 'Win Probability',
    'tender_submission_deadline' => 'Submission Deadline',
    'tender_status'              => 'Status',
    'tender_documents_url'         => 'Tender Documents Link',
    'tender_design_documents_url'  => 'Design Documents & Selection Link',
    'tender_notes'                => 'Notes',

    'no_tenders'            => 'No tenders yet',
    'no_tenders_search'     => 'No results match your search.',
    'add_first_tender'      => 'Add First Tender',
    'tender_added'          => 'Tender added successfully.',
    'tender_updated'        => 'Tender updated successfully.',
    'tender_deleted'        => 'Tender deleted successfully.',
    'total_tenders'         => 'Total Tenders',
    'all_statuses_tender'   => 'All Statuses',

    // ── Tender statuses (dynamic/manageable list) ────────────────────────────
    'status'              => 'Tender Status',
    'statuses_list'        => 'Tender Statuses',
    'statuses_subtitle'    => 'Manageable statuses — add any new one you need',
    'add_status'           => 'Add Status',
    'edit_status'          => 'Edit Status',
    'status_name'          => 'Status Name',
    'status_code'          => 'Code',
    'status_color'         => 'Color',
    'no_statuses'          => 'No statuses yet',
    'status_added'         => 'Status added successfully.',
    'status_updated'       => 'Status updated successfully.',
    'status_deleted'       => 'Status deleted successfully.',
    'status_in_use'        => 'Cannot delete a status that is used by existing tenders.',

    // ── Price quotes ──────────────────────────────────────────────────────────
    'price_quotes'            => 'Price Quotes',
    'price_quote'             => 'Price Quote',
    'price_quotes_list'       => 'Price Quotes List',
    'price_quotes_subtitle'   => 'Price quotes linked to customers and tenders',
    'add_quote'               => 'New Price Quote',
    'add_quote_subtitle'      => 'Choose the customer and add the quote lines',
    'quote_number'            => 'Quote No.',
    'quote_tender'            => 'Tender',
    'quote_date'              => 'Date',
    'quote_items'             => 'Quote Items',
    'quote_total'             => 'Total',
    'quote_added'             => 'Price quote created successfully.',
    'no_quotes'               => 'No price quotes yet',
    'total_quotes'            => 'Total Quotes',
    'attach_existing_quote'   => 'Attach an existing quote',
    'attach_quote'            => 'Attach',
    'quote_attached'          => 'Price quote attached to the tender successfully.',

];
