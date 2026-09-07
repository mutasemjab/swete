<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Country;
use App\Models\Customer;
use App\Models\PriceQuote;
use App\Models\Project;
use App\Models\Tender;
use App\Models\TenderStatus;
use Illuminate\Http\Request;

class TenderController extends ModuleController
{
    protected string $module = 'tenders';

    public function index(Request $request)
    {
        $query = Tender::query();

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('entity_name', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $tenders = $query->with(['party', 'statusRef'])->orderByDesc('submission_deadline')->paginate(20)->withQueryString();
        $statuses = TenderStatus::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.index', compact('tenders', 'statuses'));
    }

    public function create()
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $statuses  = TenderStatus::where('status', true)->orderBy('name')->get();
        $countries = Country::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.create', compact('customers', 'statuses', 'countries'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $tender = Tender::create([
            ...$validated,
            'number'     => Tender::nextNumber(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('tenders.show', $tender)
            ->with('success', __('tenders.tender_added'));
    }

    public function show(Tender $tender)
    {
        $tender->load(['party', 'statusRef', 'country', 'creator', 'priceQuotes.customer', 'projects']);
        $unlinkedQuotes = PriceQuote::whereNull('tender_id')->orderByDesc('date')->get();

        return $this->moduleView('tenders.show', compact('tender', 'unlinkedQuotes'));
    }

    public function edit(Tender $tender)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $statuses  = TenderStatus::where('status', true)->orderBy('name')->get();
        $countries = Country::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.edit', compact('tender', 'customers', 'statuses', 'countries'));
    }

    public function update(Request $request, Tender $tender)
    {
        $validated = $this->validated($request);

        $tender->update($validated);

        return redirect()->route('tenders.show', $tender)
            ->with('success', __('tenders.tender_updated'));
    }

    public function destroy(Tender $tender)
    {
        $tender->delete();

        return redirect()->route('tenders.index')
            ->with('success', __('tenders.tender_deleted'));
    }

    /** Link an existing, not-yet-attached price quote to this tender. */
    public function attachPriceQuote(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'price_quote_id' => ['required', 'exists:price_quotes,id'],
        ]);

        PriceQuote::whereNull('tender_id')->findOrFail($validated['price_quote_id'])
            ->update(['tender_id' => $tender->id]);

        return back()->with('success', __('tenders.quote_attached'));
    }

    /** Turn a won tender into a project, copying over its title and customer. */
    public function convertToProject(Request $request, Tender $tender)
    {
        $project = Project::create([
            'number'      => Project::nextNumber(),
            'tender_id'   => $tender->id,
            'customer_id' => $tender->party_id,
            'title'       => $tender->title,
            'title_en'    => $tender->title_en,
            'status'      => 'active',
            'created_by'  => $request->user()->id,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', __('tenders.project_created'));
    }

    private function validated(Request $request): array
    {
        $rules = [
            'party_id'             => ['nullable', 'exists:customers,id'],
            'title'                => ['required', 'string', 'max:255'],
            'title_en'             => ['nullable', 'string', 'max:255'],
            'entity_name'          => ['required', 'string', 'max:255'],
            'entity_name_en'       => ['nullable', 'string', 'max:255'],
            'location_scope'       => ['required', 'in:inside_jordan,outside_jordan'],
            'governorate'          => ['required_if:location_scope,inside_jordan', 'nullable', 'in:' . implode(',', array_keys(Tender::JORDAN_GOVERNORATES))],
            'country_id'           => ['required_if:location_scope,outside_jordan', 'nullable', 'exists:countries,id'],
            'tax_exempt'           => ['boolean'],
            'customs_exempt'       => ['boolean'],
            'delivery_terms'       => ['nullable', 'in:' . implode(',', Tender::DELIVERY_TERMS)],
            'coverage'             => ['nullable', 'in:' . implode(',', Tender::COVERAGE_OPTIONS)],
            'description'          => ['nullable', 'string'],
            'win_probability'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'submission_deadline'  => ['required', 'date'],
            'status_id'            => ['required', 'exists:tender_statuses,id'],
            'documents_url'        => ['nullable', 'string', 'max:255'],
            'design_documents_url' => ['nullable', 'string', 'max:255'],
            'notes'                => ['nullable', 'string'],
        ];

        $validated = $request->validate($rules);

        $validated['tax_exempt']     = $request->boolean('tax_exempt');
        $validated['customs_exempt'] = $request->boolean('customs_exempt');

        if ($validated['location_scope'] === 'outside_jordan') {
            $validated['governorate'] = null;
        } else {
            $validated['country_id'] = null;
        }

        return $validated;
    }
}
