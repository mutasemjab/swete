<?php

namespace App\Http\Controllers\ExternalPurchases;

use App\Http\Controllers\ModuleController;
use App\Models\Country;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends ModuleController
{
    protected string $module = 'external_purchases';

    public function index(Request $request)
    {
        $query = ShippingCompany::with('country');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%"));
        }

        $shippingCompanies = $query->orderBy('name')->paginate(20)->withQueryString();

        return $this->moduleView('external-purchases.shipping-companies.index', compact('shippingCompanies'));
    }

    public function create()
    {
        $countries = Country::where('status', true)->orderBy('name')->get();

        return $this->moduleView('external-purchases.shipping-companies.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        ShippingCompany::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('shipping-companies.index')
            ->with('success', __('external_purchases.shipping_company_added'));
    }

    public function edit(ShippingCompany $shippingCompany)
    {
        $countries = Country::where('status', true)->orderBy('name')->get();

        return $this->moduleView('external-purchases.shipping-companies.edit', compact('shippingCompany', 'countries'));
    }

    public function update(Request $request, ShippingCompany $shippingCompany)
    {
        $validated = $this->validated($request);

        $shippingCompany->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('shipping-companies.index')
            ->with('success', __('external_purchases.shipping_company_updated'));
    }

    public function destroy(ShippingCompany $shippingCompany)
    {
        $shippingCompany->delete();

        return redirect()->route('shipping-companies.index')
            ->with('success', __('external_purchases.shipping_company_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'name_en'    => ['nullable', 'string', 'max:150'],
            'email'      => ['required', 'email', 'max:150'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'address'    => ['nullable', 'string', 'max:255'],
            'rating'     => ['nullable', 'numeric', 'min:0', 'max:5'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'status'     => ['boolean'],
        ]);
    }
}
