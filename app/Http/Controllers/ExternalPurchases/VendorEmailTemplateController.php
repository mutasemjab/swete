<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\ModuleController;
use App\Models\VendorEmailTemplate;
use Illuminate\Http\Request;

class VendorEmailTemplateController extends ModuleController
{
    protected string $module = 'external_purchases';

    public function index()
    {
        $template = VendorEmailTemplate::current();

        return $this->moduleView('external-purchases.vendor-email-template.index', compact('template'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body'    => ['required', 'string'],
        ]);

        VendorEmailTemplate::query()->firstOrNew()->fill([
            ...$validated,
            'updated_by' => $request->user()->id,
        ])->save();

        return redirect()->route('vendor-email-template.index')
            ->with('success', __('external_purchases.vendor_email_template_saved'));
    }
}
