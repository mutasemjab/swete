<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\ClientExtraField;
use App\Models\ClientExtraFieldValue;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Governorate;
use App\Models\Material;
use App\Models\PriceQuote;
use App\Models\Project;
use App\Models\ProjectVariationOrder;
use App\Models\SalesRepresentative;
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

        // Defaults to "not yet converted" (the common case to check) unless the user explicitly
        // picks the "converted" or "all" tab.
        $converted = $request->filled('converted') ? $request->input('converted') : '0';
        if ($converted === '1') {
            $query->whereHas('projects');
        } elseif ($converted === '0') {
            $query->whereDoesntHave('projects');
        }

        $tenders = $query->with(['party', 'statusRef'])->orderByDesc('submission_deadline')->paginate(20)->withQueryString();
        $statuses = TenderStatus::where('status', true)->orderBy('name')->get();
        $notConvertedCount = Tender::whereDoesntHave('projects')->count();
        $convertedCount = Tender::whereHas('projects')->count();

        return $this->moduleView('tenders.index', compact('tenders', 'statuses', 'notConvertedCount', 'convertedCount'));
    }

    public function create()
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $statuses  = TenderStatus::where('status', true)->orderBy('name')->get();
        $countries = Country::where('status', true)->orderBy('name')->get();
        $currencies = Currency::where('status', true)->orderBy('name')->get();
        $salesReps = SalesRepresentative::where('status', true)->orderBy('name')->get();
        $extraFields = ClientExtraField::forTenders()->active()->orderBy('sort_order')->get();

        $governorates = Governorate::selectable();

        return $this->moduleView('tenders.create', compact('customers', 'statuses', 'countries', 'currencies', 'governorates', 'salesReps', 'extraFields'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $tender = Tender::create([
            ...$validated,
            'number'     => Tender::nextNumber(),
            'created_by' => $request->user()->id,
        ]);

        $this->syncExtraFieldValues($tender, $request);

        return redirect()->route('tenders.show', $tender)
            ->with('success', __('tenders.tender_added'));
    }

    public function show(Tender $tender)
    {
        $tender->load([
            'party', 'statusRef', 'governorate', 'country', 'creator',
            // Every revision shown, not just the current one — so any of them can still be
            // converted to a project from here (not only the latest edit of each quote).
            'priceQuotes' => fn ($query) => $query->with('customer')->orderBy('tender_sequence')->orderBy('version'),
            'projects', 'extraFieldValues.field', 'analyses.items.material', 'analyses.items.x8', 'analysisAttachments',
        ]);
        $unlinkedQuotes = PriceQuote::whereNull('tender_id')->orderByDesc('date')->get();

        return $this->moduleView('tenders.show', compact('tender', 'unlinkedQuotes'));
    }

    public function edit(Tender $tender)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $statuses  = TenderStatus::where('status', true)->orderBy('name')->get();
        $countries = Country::where('status', true)->orderBy('name')->get();
        $currencies = Currency::where('status', true)->orderBy('name')->get();
        $salesReps = SalesRepresentative::where('status', true)->orderBy('name')->get();
        $extraFields = ClientExtraField::forTenders()->active()->orderBy('sort_order')->get();
        $extraFieldValues = ClientExtraFieldValue::where('entity_type', 'tender')
            ->where('entity_id', $tender->id)
            ->get()
            ->keyBy('field_id');

        $governorates = Governorate::selectable($tender->governorate_id);

        return $this->moduleView('tenders.edit', compact('tender', 'customers', 'statuses', 'countries', 'currencies', 'governorates', 'salesReps', 'extraFields', 'extraFieldValues'));
    }

    public function update(Request $request, Tender $tender)
    {
        $validated = $this->validated($request);

        $tender->update($validated);

        $this->syncExtraFieldValues($tender, $request);

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

    /**
     * Turn a specific won price quote into a project.
     *
     * - No project at all yet for this tender → this conversion creates the project itself.
     * - An ACTIVE (already-approved) project exists → this is a mid-project change requested by
     *   the customer: a Variation Order queued for its own review, never touching the live
     *   project's status directly. Blocked outright for a different customer — once approved,
     *   that's final.
     * - An UNDER-REVIEW (not yet approved) project exists → nothing has been decided yet, so
     *   silently picking "add a Variation Order" or blocking a different customer would be
     *   guessing. Requires an explicit `mode` ('add'|'replace') from the user instead — see the
     *   Add/Replace prompt on the tender page (resources/views/tenders/show.blade.php).
     */
    public function convertToProject(Request $request, PriceQuote $priceQuote)
    {
        abort_unless($priceQuote->tender_id, 404);
        abort_unless(! $priceQuote->isLocked(), 403);

        if ($priceQuote->hasPendingVariationOrder()) {
            return back()->with('error', __('tenders.quote_already_pending_vo'));
        }

        $tender = $priceQuote->tender;

        $activeProject = Project::where('tender_id', $tender->id)->where('status', 'active')->where('is_current', true)->latest()->first();
        $underReviewProject = Project::where('tender_id', $tender->id)->where('status', 'under_review')->where('is_current', true)->latest()->first();

        if ($activeProject) {
            if ($activeProject->customer_id !== $priceQuote->customer_id) {
                return back()->with('error', __('tenders.quote_customer_mismatch'));
            }

            $activeProject->increment('variation_round');

            ProjectVariationOrder::create([
                'project_id'     => $activeProject->id,
                'price_quote_id' => $priceQuote->id,
                'status'         => 'pending',
                'created_by'     => $request->user()->id,
            ]);

            $this->promoteDraftMaterials($tender);

            return redirect()->route('project-reviews.index')
                ->with('success', __('tenders.variation_order_created'));
        }

        if ($underReviewProject) {
            $mode = $request->input('mode');

            if (! in_array($mode, ['add', 'replace'], true)) {
                return back()->with('error', __('tenders.quote_conversion_choice_required'));
            }

            if ($mode === 'add') {
                $underReviewProject->increment('variation_round');

                ProjectVariationOrder::create([
                    'project_id'     => $underReviewProject->id,
                    'price_quote_id' => $priceQuote->id,
                    'status'         => 'pending',
                    'created_by'     => $request->user()->id,
                ]);

                $this->promoteDraftMaterials($tender);

                return redirect()->route('project-reviews.index')
                    ->with('success', __('tenders.variation_order_created'));
            }

            // $mode === 'replace': the project hasn't been approved yet, so there's nothing to
            // "split" — just swap its own origin quote (and customer, if it differs) outright.
            $underReviewProject->update([
                'price_quote_id' => $priceQuote->id,
                'customer_id'    => $priceQuote->customer_id,
            ]);

            $this->promoteDraftMaterials($tender);

            return redirect()->route('projects.show', $underReviewProject)
                ->with('success', __('tenders.quote_replaced_on_project'));
        }

        $project = Project::create([
            'number'         => Project::nextNumber(),
            'tender_id'      => $tender->id,
            'customer_id'    => $tender->party_id,
            'price_quote_id' => $priceQuote->id,
            'title'          => $tender->title,
            'title_en'       => $tender->title_en,
            'status'         => 'under_review',
            'created_by'     => $request->user()->id,
        ]);

        $this->promoteDraftMaterials($tender);

        return redirect()->route('projects.show', $project)
            ->with('success', __('tenders.project_created'));
    }

    /**
     * A material quick-added from a Price Quote screen stays a "draft" (hidden from the real
     * Warehouse catalog) until the tender it was priced for is actually won — this is that
     * promotion. Only materials referenced by THIS tender's own price quotes are promoted; a
     * draft used only in an unrelated/still-pending quote stays draft.
     */
    private function promoteDraftMaterials(Tender $tender): void
    {
        $materialIds = $tender->priceQuotes()->with('items')->get()
            ->flatMap(fn ($quote) => $quote->items)
            ->pluck('material_id')
            ->unique();

        if ($materialIds->isNotEmpty()) {
            Material::where('is_draft', true)->whereIn('id', $materialIds)->update(['is_draft' => false]);
        }
    }

    private function syncExtraFieldValues(Tender $tender, Request $request): void
    {
        $fields = ClientExtraField::forTenders()->active()->get();

        foreach ($fields as $field) {
            if ($field->field_type === 'image') {
                if ($request->hasFile("extra_fields_images.{$field->id}")) {
                    $filename = uploadImage(
                        'assets/uploads/clients/extra-fields',
                        $request->file("extra_fields_images.{$field->id}")
                    );
                    ClientExtraFieldValue::updateOrCreate(
                        ['field_id' => $field->id, 'entity_type' => 'tender', 'entity_id' => $tender->id],
                        ['file_path' => "assets/uploads/clients/extra-fields/{$filename}", 'value' => null]
                    );
                }
            } else {
                $value = $request->input("extra_fields.{$field->id}");
                ClientExtraFieldValue::updateOrCreate(
                    ['field_id' => $field->id, 'entity_type' => 'tender', 'entity_id' => $tender->id],
                    ['value' => $value ?: null, 'file_path' => null]
                );
            }
        }
    }

    private function validated(Request $request): array
    {
        // A pasted link with no scheme would otherwise render as a relative href and silently fail to open.
        $request->merge([
            'documents_url'        => normalizeUrl($request->input('documents_url')),
            'design_documents_url' => normalizeUrl($request->input('design_documents_url')),
        ]);

        $rules = [
            'party_id'             => ['nullable', 'exists:customers,id'],
            'sales_rep_id'         => ['nullable', 'exists:sales_representatives,id'],
            'title'                => ['required', 'string', 'max:255'],
            'title_en'             => ['nullable', 'string', 'max:255'],
            'entity_name'          => ['required', 'string', 'max:255'],
            'entity_name_en'       => ['nullable', 'string', 'max:255'],
            'location_scope'       => ['required', 'in:inside_jordan,outside_jordan'],
            'governorate_id'          => ['required_if:location_scope,inside_jordan', 'nullable', 'exists:governorates,id'],
            'country_id'           => ['required_if:location_scope,outside_jordan', 'nullable', 'exists:countries,id'],
            'tax_exempt'           => ['boolean'],
            'customs_exempt'       => ['boolean'],
            'delivery_terms'       => ['nullable', 'in:' . implode(',', Tender::DELIVERY_TERMS)],
            'coverage'             => ['nullable', 'in:' . implode(',', Tender::COVERAGE_OPTIONS)],
            'currency_id'          => ['nullable', 'exists:currencies,id'],
            'description'          => ['nullable', 'string'],
            'win_probability'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'submission_deadline'  => ['required', 'date'],
            'status_id'            => ['required', 'exists:tender_statuses,id'],
            'documents_url'        => ['nullable', 'url', 'max:255'],
            'design_documents_url' => ['nullable', 'url', 'max:255'],
            'notes'                => ['nullable', 'string'],
            'extra_fields'          => ['nullable', 'array'],
            'extra_fields.*'        => ['nullable', 'string'],
            'extra_fields_images'   => ['nullable', 'array'],
            'extra_fields_images.*' => ['nullable', 'image', 'max:4096'],
        ];

        $validated = $request->validate($rules);

        $validated['tax_exempt']     = $request->boolean('tax_exempt');
        $validated['customs_exempt'] = $request->boolean('customs_exempt');

        if ($validated['location_scope'] === 'outside_jordan') {
            $validated['governorate_id'] = null;
        } else {
            $validated['country_id'] = null;
        }

        return $validated;
    }
}
