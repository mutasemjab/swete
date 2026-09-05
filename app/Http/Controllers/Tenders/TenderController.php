<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Tender;
use Illuminate\Http\Request;

class TenderController extends ModuleController
{
    protected string $module = 'tenders';

    public function index(Request $request)
    {
        $query = Tender::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('entity_name', 'like', "%{$search}%")
                ->orWhere('number', 'like', "%{$search}%"));
        }

        $tenders = $query->orderByDesc('submission_deadline')->paginate(20)->withQueryString();

        return $this->moduleView('tenders.index', compact('tenders'));
    }

    public function create()
    {
        return $this->moduleView('tenders.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        Tender::create([
            ...$validated,
            'number'     => Tender::nextNumber(),
            'status'     => $validated['status'] ?? 'open',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('tenders.index')
            ->with('success', __('tenders.tender_added'));
    }

    public function edit(Tender $tender)
    {
        return $this->moduleView('tenders.edit', compact('tender'));
    }

    public function update(Request $request, Tender $tender)
    {
        $validated = $this->validated($request);

        $tender->update($validated);

        return redirect()->route('tenders.index')
            ->with('success', __('tenders.tender_updated'));
    }

    public function destroy(Tender $tender)
    {
        $tender->delete();

        return redirect()->route('tenders.index')
            ->with('success', __('tenders.tender_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'                => ['required', 'string', 'max:255'],
            'title_en'             => ['nullable', 'string', 'max:255'],
            'entity_name'          => ['required', 'string', 'max:255'],
            'entity_name_en'       => ['nullable', 'string', 'max:255'],
            'submission_deadline'  => ['required', 'date'],
            'status'               => ['nullable', 'in:open,closed,won,lost'],
            'notes'                => ['nullable', 'string'],
        ]);
    }
}
