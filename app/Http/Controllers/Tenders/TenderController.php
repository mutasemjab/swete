<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\Tender;
use Illuminate\Http\Request;

class TenderController extends ModuleController
{
    protected string $module = 'tenders';

    public function index(Request $request, string $type)
    {
        $query = Tender::ofType($type);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('entity_name', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $tenders = $query->with('party')->orderByDesc('submission_deadline')->paginate(20)->withQueryString();

        return $this->moduleView('tenders.index', compact('tenders', 'type'));
    }

    public function create(string $type)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.create', compact('type', 'customers'));
    }

    public function store(Request $request, string $type)
    {
        $validated = $this->validated($request, $type);

        Tender::create([
            ...$validated,
            'type'       => $type,
            'number'     => Tender::nextNumber($type),
            'status'     => $validated['status'] ?? 'open',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route("{$this->routePrefix($type)}.index")
            ->with('success', __('tenders.tender_added'));
    }

    public function edit(Tender $tender, string $type)
    {
        $customers = Customer::where('status', true)->orderBy('name')->get();

        return $this->moduleView('tenders.edit', compact('type', 'tender', 'customers'));
    }

    public function update(Request $request, Tender $tender, string $type)
    {
        $validated = $this->validated($request, $type);

        $tender->update($validated);

        return redirect()->route("{$this->routePrefix($type)}.index")
            ->with('success', __('tenders.tender_updated'));
    }

    public function destroy(Tender $tender, string $type)
    {
        $tender->delete();

        return redirect()->route("{$this->routePrefix($type)}.index")
            ->with('success', __('tenders.tender_deleted'));
    }

    private function routePrefix(string $type): string
    {
        return $type === 'service_call' ? 'service-calls' : 'tenders';
    }

    private function validated(Request $request, string $type): array
    {
        $rules = [
            'party_id'             => ['nullable', 'exists:customers,id'],
            'title'                => ['required', 'string', 'max:255'],
            'title_en'             => ['nullable', 'string', 'max:255'],
            'entity_name'          => ['required', 'string', 'max:255'],
            'entity_name_en'       => ['nullable', 'string', 'max:255'],
            'description'          => ['nullable', 'string'],
            'win_probability'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'submission_deadline'  => ['required', 'date'],
            'status'               => ['nullable', 'in:open,closed,won,lost'],
            'notes'                => ['nullable', 'string'],
        ];

        if ($type === 'service_call') {
            $rules['governorate'] = ['required', 'in:' . implode(',', array_keys(Tender::JORDAN_GOVERNORATES))];
        } else {
            $rules['location_scope'] = ['required', 'in:inside_jordan,outside_jordan'];
            $rules['governorate']    = ['required_if:location_scope,inside_jordan', 'nullable', 'in:' . implode(',', array_keys(Tender::JORDAN_GOVERNORATES))];
        }

        $validated = $request->validate($rules);

        if ($type === 'service_call') {
            $validated['location_scope'] = 'inside_jordan';
        } elseif ($validated['location_scope'] === 'outside_jordan') {
            $validated['governorate'] = null;
        }

        return $validated;
    }
}
