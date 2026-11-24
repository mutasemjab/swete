<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\ModuleController;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends ModuleController
{
    protected string $module = 'equipment';

    public function index()
    {
        $types = DocumentType::orderBy('name')->get();

        return $this->moduleView('equipment.document-types.index', compact('types'));
    }

    public function create()
    {
        return $this->moduleView('equipment.document-types.create', ['type' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        DocumentType::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('document-types.index')
            ->with('success', __('equipment.document_type_added'));
    }

    public function edit(DocumentType $documentType)
    {
        return $this->moduleView('equipment.document-types.edit', ['type' => $documentType]);
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $this->validated($request);

        $documentType->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('document-types.index')
            ->with('success', __('equipment.document_type_updated'));
    }

    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documents()->exists()) {
            return back()->with('error', __('equipment.document_type_in_use'));
        }

        $documentType->delete();

        return redirect()->route('document-types.index')
            ->with('success', __('equipment.document_type_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:150', 'unique:document_types,name,'.$request->route('document_type')?->id],
            'name_en' => ['nullable', 'string', 'max:150'],
        ]);
    }
}
