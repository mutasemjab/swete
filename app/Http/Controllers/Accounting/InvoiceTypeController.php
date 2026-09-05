<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\InvoiceType;
use Illuminate\Http\Request;

class InvoiceTypeController extends ModuleController
{
    protected string $module = 'accounting';

    public function index()
    {
        $invoiceTypes = InvoiceType::orderByDesc('is_system')->orderBy('name')->get();
        return $this->moduleView('accounting.invoice-types.index', compact('invoiceTypes'));
    }

    public function create()
    {
        return $this->moduleView('accounting.invoice-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'       => ['required', 'string', 'max:50', 'alpha_dash', 'unique:invoice_types,code'],
            'name'       => ['required', 'string', 'max:100'],
            'name_en'    => ['nullable', 'string', 'max:100'],
            'party_type' => ['required', 'in:customer,supplier'],
            'status'     => ['boolean'],
        ]);

        InvoiceType::create([
            ...$validated,
            'is_system' => false,
            'status'    => $request->boolean('status', true),
        ]);

        return redirect()->route('accounting.invoice-types.index')
            ->with('success', __('accounting.invoice_type_added'));
    }

    public function edit(InvoiceType $invoiceType)
    {
        return $this->moduleView('accounting.invoice-types.edit', compact('invoiceType'));
    }

    public function update(Request $request, InvoiceType $invoiceType)
    {
        $validated = $request->validate([
            'code'       => ['required', 'string', 'max:50', 'alpha_dash', "unique:invoice_types,code,{$invoiceType->id}"],
            'name'       => ['required', 'string', 'max:100'],
            'name_en'    => ['nullable', 'string', 'max:100'],
            'party_type' => ['required', 'in:customer,supplier'],
            'status'     => ['boolean'],
        ]);

        $invoiceType->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('accounting.invoice-types.index')
            ->with('success', __('accounting.invoice_type_updated'));
    }

    public function destroy(InvoiceType $invoiceType)
    {
        if ($invoiceType->is_system) {
            return back()->with('error', __('accounting.invoice_type_is_system'));
        }

        $invoiceType->delete();

        return redirect()->route('accounting.invoice-types.index')
            ->with('success', __('accounting.invoice_type_deleted'));
    }
}
