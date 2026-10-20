<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceReceipt;
use Illuminate\Http\Request;

class InvoiceReceiptController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'date'   => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.001'],
            'notes'  => ['nullable', 'string'],
        ]);

        $invoice->receipts()->create([
            ...$validated,
            'number'     => InvoiceReceipt::nextNumber(),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', __('accounting.receipt_voucher_added'));
    }

    public function destroy(Invoice $invoice, InvoiceReceipt $receipt)
    {
        abort_unless($receipt->invoice_id === $invoice->id, 404);

        $receipt->delete();

        return back()->with('success', __('accounting.receipt_voucher_deleted'));
    }
}
