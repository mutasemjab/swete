<?php

namespace App\Http\Controllers\Agreements;

use App\Http\Controllers\ModuleController;
use App\Models\Agreement;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AgreementController extends ModuleController
{
    protected string $module = 'agreements';

    public function index(Request $request)
    {
        $query = Agreement::query();

        if ($request->filled('party_type')) {
            $query->where('party_type', $request->input('party_type'));
        }

        $agreements = $query->latest()->paginate(20)->withQueryString();

        // party() isn't a real polymorphic relation (same convention as Invoice::party()), so it
        // can't be eager-loaded with ->with() — load each row's party individually instead.
        $agreements->each(fn ($agreement) => $agreement->setRelation('party', $agreement->party()->first()));

        return $this->moduleView('agreements.index', [
            'agreements' => $agreements,
            'customers'  => Customer::where('status', true)->orderBy('name')->get(),
            'suppliers'  => Supplier::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $project = $request->filled('project_id') ? Project::find($request->input('project_id')) : null;

        return $this->moduleView('agreements.create', [
            'agreement' => null,
            'project'   => $project,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $filename = uploadImage('assets/uploads/agreements', $validated['file']);

        $agreement = Agreement::create([
            'number'     => Agreement::nextNumber(),
            'party_type' => $validated['party_type'],
            'party_id'   => $validated['party_id'],
            'project_id' => $validated['project_id'] ?? null,
            'path'       => 'assets/uploads/agreements/' . $filename,
            'notes'      => $validated['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        if ($agreement->project_id) {
            return redirect()->route('projects.show', $agreement->project_id)
                ->with('success', __('agreements.agreement_added'));
        }

        return redirect()->route('agreements.index')
            ->with('success', __('agreements.agreement_added'));
    }

    public function show(Agreement $agreement)
    {
        $agreement->load('creator');
        $agreement->setRelation('party', $agreement->party()->first());

        return $this->moduleView('agreements.show', ['agreement' => $agreement]);
    }

    public function edit(Agreement $agreement)
    {
        $agreement->setRelation('party', $agreement->party()->first());

        return $this->moduleView('agreements.edit', [
            'agreement' => $agreement,
            'customers' => Customer::where('status', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Agreement $agreement)
    {
        $validated = $this->validated($request, true);

        $path = $agreement->path;

        if ($request->hasFile('file')) {
            if (file_exists(base_path($path))) {
                @unlink(base_path($path));
            }

            $path = 'assets/uploads/agreements/' . uploadImage('assets/uploads/agreements', $validated['file']);
        }

        $agreement->update([
            'party_type' => $validated['party_type'],
            'party_id'   => $validated['party_id'],
            'path'       => $path,
            'notes'      => $validated['notes'] ?? null,
        ]);

        return redirect()->route('agreements.show', $agreement)
            ->with('success', __('agreements.agreement_updated'));
    }

    public function destroy(Agreement $agreement)
    {
        if (file_exists(base_path($agreement->path))) {
            @unlink(base_path($agreement->path));
        }

        $agreement->delete();

        return redirect()->route('agreements.index')
            ->with('success', __('agreements.agreement_deleted'));
    }

    private function validated(Request $request, bool $fileOptional = false): array
    {
        return $request->validate([
            'party_type' => ['required', 'in:customer,supplier'],
            'party_id'   => [
                'required',
                $request->input('party_type') === 'supplier' ? 'exists:suppliers,id' : 'exists:customers,id',
            ],
            'project_id' => ['nullable', 'exists:projects,id'],
            'file'       => [$fileOptional ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:10240'],
            'notes'      => ['nullable', 'string'],
        ]);
    }
}
