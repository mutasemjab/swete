<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\ModuleController;
use App\Models\Invoice;
use App\Models\InvoiceType;
use App\Models\MaintenanceContract;
use App\Models\MaintenanceContractPayment;
use App\Models\User;
use Illuminate\Http\Request;

/** Mirrors Tenders\PriceQuoteController's assign()/convertToInvoice() shape — see its comments for the design rationale. */
class ContractPaymentController extends ModuleController
{
    protected string $module = 'maintenance';

    public function index(Request $request)
    {
        $query = MaintenanceContractPayment::with(['contract.customer', 'currency', 'assignee', 'invoice']);

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        $payments  = $query->orderBy('due_date')->paginate(20)->withQueryString();
        $employees = User::where('status', true)->orderBy('name')->get();

        return $this->moduleView('maintenance.contracts.payments-index', compact('payments', 'employees'));
    }

    public function store(Request $request, MaintenanceContract $maintenanceContract)
    {
        $validated = $request->validate([
            'due_date'    => ['required', 'date'],
            'amount'      => ['required', 'numeric', 'min:0.001'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'notes'       => ['nullable', 'string'],
        ]);

        $maintenanceContract->payments()->create($validated);

        return back()->with('success', __('maintenance.payment_added'));
    }

    public function destroy(MaintenanceContract $maintenanceContract, MaintenanceContractPayment $payment)
    {
        abort_unless($payment->contract_id === $maintenanceContract->id, 404);

        if ($payment->invoice_id) {
            return back()->with('error', __('maintenance.payment_already_converted'));
        }

        $payment->delete();

        return back()->with('success', __('maintenance.payment_deleted'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'payment_ids'   => ['required', 'array', 'min:1'],
            'payment_ids.*' => ['exists:maintenance_contract_payments,id'],
            'assigned_to'   => ['required', 'exists:users,id'],
        ]);

        $updated = MaintenanceContractPayment::whereIn('id', $validated['payment_ids'])
            ->whereNull('invoice_id')
            ->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', __('maintenance.payment_sent_to_employee', ['count' => $updated]));
    }

    public function convertToInvoice(Request $request, MaintenanceContractPayment $payment)
    {
        abort_unless($payment->assigned_to === $request->user()->id, 403, __('maintenance.payment_convert_unauthorized'));

        if ($payment->invoice_id) {
            return back()->with('error', __('maintenance.payment_already_converted'));
        }

        $payment->load('contract.customer');

        $type = InvoiceType::where('party_type', 'customer')->where('status', true)->first();

        if (! $type) {
            return back()->with('error', __('maintenance.payment_no_invoice_type'));
        }

        $invoice = Invoice::create([
            'invoice_type_id' => $type->id,
            'party_type'      => 'customer',
            'party_id'        => $payment->contract->customer_id,
            'number'          => Invoice::nextNumber($type),
            'date'            => now()->toDateString(),
            'currency_id'     => $payment->currency_id,
            'notes'           => __('maintenance.payment_convert_invoice_note', ['number' => $payment->contract->number]),
            'status'          => 'draft',
            'created_by'      => $request->user()->id,
        ]);

        $invoice->items()->create([
            'description' => __('maintenance.payment_invoice_item_description', [
                'number' => $payment->contract->number,
                'date'   => $payment->due_date->format('Y-m-d'),
            ]),
            'quantity'   => 1,
            'unit_price' => $payment->amount,
            'total'      => $payment->amount,
        ]);

        $invoice->recalculateTotals();

        $payment->update(['invoice_id' => $invoice->id]);

        return redirect()->route('accounting.invoices.show', $invoice)
            ->with('success', __('maintenance.payment_converted'));
    }
}
