<?php

return [

    // ── Module ───────────────────────────────────────────────────────────────
    'module_name' => 'Split Units',
    'module_desc' => 'Manage price quotes for split AC units',

    // ── Sidebar sections ─────────────────────────────────────────────────────
    'section_quotes' => 'Split Units',

    // ── Sidebar items ────────────────────────────────────────────────────────
    'nav_list' => 'Split Units List',
    'nav_add'  => 'New Unit',

    // ── Index ────────────────────────────────────────────────────────────────
    'split_units' => 'Split Units',
    'list'        => 'Split Units List',
    'subtitle'    => 'Price quotes for split AC units',
    'add'         => 'Add Unit',
    'add_subtitle'=> 'Select a customer and add line items for the unit quote',
    'edit'        => 'Edit Split Unit',
    'total'       => 'Total Units',
    'no_quotes'   => 'No split units yet',

    // ── Fields ───────────────────────────────────────────────────────────────
    'split_unit'    => 'Split Unit',
    'number'        => 'Unit No.',
    'date'          => 'Date',
    'validity_weeks'=> 'Validity (Weeks)',
    'assigned_to'   => 'Assigned To',
    'unassigned'    => 'Unassigned',

    // ── Status badges ────────────────────────────────────────────────────────
    'status_invoiced'        => 'Invoiced',
    'status_pending_invoice' => 'Pending Invoice',

    // ── Filters ──────────────────────────────────────────────────────────────
    'all_employees' => 'All Employees',

    // ── Items ────────────────────────────────────────────────────────────────
    'items'               => 'Line Items',
    'item_notes'          => 'Item Notes',
    'item_note_placeholder' => 'Note',
    'add_item_note'       => 'Add Note',

    // ── Customer history panel ────────────────────────────────────────────────
    'customer_history'        => 'Previous units for this customer',
    'customer_history_empty'  => 'No previous units for this customer.',

    // ── Flash messages ────────────────────────────────────────────────────────
    'quote_added'            => 'Split unit created successfully.',
    'quote_updated'          => 'Split unit updated successfully.',
    'quote_deleted'          => 'Split unit deleted successfully.',
    'quote_discount_exceeds' => 'Discount cannot exceed the subtotal.',

    // ── Very Important Notes (print) ─────────────────────────────────────────
    'and'                            => 'and',
    'quote_note_validity'            => 'Offer validity: :weeks weeks from the date of the quote.',
    'quote_note_technical_submittal' => 'All technical information as per our approved Submittal.',
    'quote_note_supply_scope'        => 'Prices include :scope.',
    'quote_note_winching_included'   => 'Price includes winching works.',
    'quote_note_winching_excluded'   => 'Price excludes winching works.',
    'quote_note_delivery_term'       => 'Price includes :term.',
    'quote_item_sales_tax'           => 'Sales Tax',
    'quote_item_customs_fees'        => 'Customs Fees',
    'quote_note_prices_include'      => 'Prices include :items.',
    'quote_note_prices_exclude'      => 'Prices exclude :items.',
    'quote_note_boiler'              => 'Please note that in some cases the boiler may need to operate when it is necessary to raise the tank water temperature above 55°C or during periods of high hot water demand.',
    'quote_note_currency'            => 'Prices are in :code.',

    // ── Print document ────────────────────────────────────────────────────────
    'print_prepared_by'          => 'Prepared By',
    'print_number'               => 'Unit No.',
    'print_full_price'           => 'Full Price',
    'print_very_important_notes' => 'Very Important Notes',

];
