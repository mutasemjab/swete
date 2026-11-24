<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\ModuleController;
use App\Models\Document;
use App\Models\DocumentAssignment;
use App\Models\DocumentType;
use App\Models\EquipmentCategory;
use App\Models\EquipmentManufacturer;
use App\Models\EquipmentModel;
use App\Models\EquipmentSubtype;
use App\Models\EquipmentUnit;
use App\Support\DocumentInheritance;
use App\Support\FileStorage;
use Illuminate\Http\Request;

class DocumentController extends ModuleController
{
    protected string $module = 'equipment';

    public function index(Request $request)
    {
        $query = Document::with(['type', 'assignment']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('tags', 'like', "%{$search}%"));
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->input('document_type_id'));
        }

        if (! $request->boolean('show_archived')) {
            $query->where('is_archived', false);
        }

        $documents = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $types = DocumentType::where('status', true)->orderBy('name')->get();

        return $this->moduleView('equipment.documents.index', compact('documents', 'types'));
    }

    public function create()
    {
        return $this->moduleView('equipment.documents.create', [
            'document' => null,
        ] + $this->pickers());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $document = Document::create([
            'document_type_id' => $validated['document_type_id'],
            'name'             => $validated['name'],
            'name_en'          => $validated['name_en'] ?? null,
            'language'         => $validated['language'],
            'revision'         => $validated['revision'] ?? null,
            'tags'             => $validated['tags'] ?? null,
            'path'             => FileStorage::store($validated['file'], 'documents'),
            'original_name'    => $validated['file']->getClientOriginalName(),
            'content_type'     => $validated['file']->getMimeType(),
            'size'             => $validated['file']->getSize(),
            'uploaded_by'      => $request->user()->id,
        ]);

        $assignment = $this->syncAssignment($document, $validated, $request->user()->id);

        return redirect()->route('documents.index')
            ->with('success', __('equipment.document_added', ['count' => DocumentInheritance::reachCount($assignment)]));
    }

    public function edit(Document $document)
    {
        $document->load('assignment');

        return $this->moduleView('equipment.documents.edit', [
            'document' => $document,
        ] + $this->pickers());
    }

    public function update(Request $request, Document $document)
    {
        $validated = $this->validated($request, $document);

        $document->update([
            'document_type_id' => $validated['document_type_id'],
            'name'              => $validated['name'],
            'name_en'           => $validated['name_en'] ?? null,
            'language'          => $validated['language'],
            'revision'          => $validated['revision'] ?? null,
            'tags'              => $validated['tags'] ?? null,
        ]);

        if (! empty($validated['file'])) {
            FileStorage::delete($document->path);
            $document->update([
                'path'          => FileStorage::store($validated['file'], 'documents'),
                'original_name' => $validated['file']->getClientOriginalName(),
                'content_type'  => $validated['file']->getMimeType(),
                'size'          => $validated['file']->getSize(),
            ]);
        }

        $assignment = $this->syncAssignment($document, $validated, $request->user()->id);

        return redirect()->route('documents.index')
            ->with('success', __('equipment.document_updated', ['count' => DocumentInheritance::reachCount($assignment)]));
    }

    public function archive(Document $document)
    {
        $document->update(['is_archived' => ! $document->is_archived]);

        return back()->with('success', $document->is_archived
            ? __('equipment.document_archived')
            : __('equipment.document_unarchived'));
    }

    public function destroy(Document $document)
    {
        FileStorage::delete($document->path);
        $document->delete(); // its DocumentAssignment row cascades at the DB level

        return redirect()->route('documents.index')
            ->with('success', __('equipment.document_deleted'));
    }

    private function syncAssignment(Document $document, array $validated, int $userId): DocumentAssignment
    {
        $document->assignment()->delete();

        return $document->assignment()->create([
            'level'                     => $validated['level'],
            'equipment_unit_id'         => $validated['level'] === 'unit' ? $validated['equipment_unit_id'] : null,
            'equipment_model_id'        => $validated['level'] === 'model' ? $validated['equipment_model_id'] : null,
            'equipment_subtype_id'      => $validated['level'] === 'subtype' ? $validated['equipment_subtype_id'] : null,
            'equipment_category_id'     => $validated['level'] === 'category' ? $validated['equipment_category_id'] : null,
            'equipment_manufacturer_id' => $validated['level'] === 'manufacturer' ? $validated['equipment_manufacturer_id'] : null,
            'created_by'                => $userId,
        ]);
    }

    private function pickers(): array
    {
        return [
            'types'         => DocumentType::where('status', true)->orderBy('name')->get(),
            'units'         => EquipmentUnit::where('is_active', true)->orderBy('name')->get(['id', 'name', 'name_en']),
            'models'        => EquipmentModel::where('status', true)->orderBy('model_number')->get(['id', 'model_number']),
            'subtypes'      => EquipmentSubtype::where('status', true)->orderBy('name')->get(['id', 'name', 'name_en']),
            'categories'    => EquipmentCategory::where('status', true)->orderBy('name')->get(['id', 'name', 'name_en']),
            'manufacturers' => EquipmentManufacturer::where('status', true)->orderBy('name')->get(['id', 'name', 'name_en']),
        ];
    }

    private function validated(Request $request, ?Document $document = null): array
    {
        $rules = [
            'document_type_id' => ['required', 'exists:document_types,id'],
            'name'              => ['required', 'string', 'max:255'],
            'name_en'           => ['nullable', 'string', 'max:255'],
            'language'          => ['required', 'in:ar,en,both,other'],
            'revision'          => ['nullable', 'string', 'max:40'],
            'tags'              => ['nullable', 'string', 'max:300'],
            'file'              => [$document ? 'nullable' : 'required', 'file', 'max:20480'],
            'level'             => ['required', 'in:'.implode(',', DocumentAssignment::LEVELS)],
            'equipment_unit_id'         => ['required_if:level,unit', 'nullable', 'exists:equipment_units,id'],
            'equipment_model_id'        => ['required_if:level,model', 'nullable', 'exists:equipment_models,id'],
            'equipment_subtype_id'      => ['required_if:level,subtype', 'nullable', 'exists:equipment_subtypes,id'],
            'equipment_category_id'     => ['required_if:level,category', 'nullable', 'exists:equipment_categories,id'],
            'equipment_manufacturer_id' => ['required_if:level,manufacturer', 'nullable', 'exists:equipment_manufacturers,id'],
        ];

        return $request->validate($rules);
    }
}
