<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\Customer;
use App\Models\EquipmentCategory;
use App\Models\EquipmentManufacturer;
use App\Models\EquipmentModel;
use App\Models\EquipmentSubtype;
use App\Models\EquipmentUnit;
use App\Models\MaintenanceReportTemplate;
use App\Support\DocumentInheritance;
use App\Support\ReportTemplateResolver;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EquipmentUnitController extends ModuleController
{
    protected string $module = 'equipment';

    public function show(EquipmentUnit $equipmentUnit)
    {
        $equipmentUnit->load(['customer', 'category', 'subtype', 'model.manufacturer', 'creator']);
        $documents = DocumentInheritance::forUnit($equipmentUnit);
        $suggestedTemplate = ReportTemplateResolver::forUnit($equipmentUnit);

        return $this->moduleView('equipment.units.show', compact('equipmentUnit', 'documents', 'suggestedTemplate'));
    }

    /** The scannable SVG image itself, encoding the URL a phone's camera should open. */
    public function qr(EquipmentUnit $equipmentUnit): Response
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data(route('equipment-units.scan', $equipmentUnit->qr_token))
            ->size(300)
            ->margin(10)
            ->build();

        return response($result->getString(), 200, ['Content-Type' => $result->getMimeType()]);
    }

    /** Where a scanned QR code lands — resolves the token and hands off to the normal, permission-checked show page. */
    public function scan(string $token)
    {
        $equipmentUnit = EquipmentUnit::where('qr_token', $token)->firstOrFail();

        return redirect()->route('equipment-units.show', $equipmentUnit);
    }

    public function index(Request $request)
    {
        $query = EquipmentUnit::with(['customer', 'category', 'subtype', 'model.manufacturer']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%"));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('equipment_category_id')) {
            $query->where('equipment_category_id', $request->input('equipment_category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if (! $request->boolean('show_archived')) {
            $query->where('is_active', true);
        }

        $units = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $customers = Customer::where('status', true)->orderBy('name')->get();
        $categories = EquipmentCategory::where('status', true)->orderBy('name')->get();

        return $this->moduleView('equipment.units.index', compact('units', 'customers', 'categories'));
    }

    public function create()
    {
        return $this->moduleView('equipment.units.create', [
            'unit' => null,
        ] + $this->pickers());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        EquipmentUnit::create([
            ...$validated,
            'reading_limits' => $this->readingLimits($request),
            'is_active'      => true,
            'created_by'     => $request->user()->id,
        ]);

        return redirect()->route('equipment-units.index')
            ->with('success', __('equipment.unit_added'));
    }

    public function edit(EquipmentUnit $equipmentUnit)
    {
        return $this->moduleView('equipment.units.edit', [
            'unit' => $equipmentUnit,
        ] + $this->pickers());
    }

    public function update(Request $request, EquipmentUnit $equipmentUnit)
    {
        $validated = $this->validated($request);

        $equipmentUnit->update([...$validated, 'reading_limits' => $this->readingLimits($request)]);

        return redirect()->route('equipment-units.index')
            ->with('success', __('equipment.unit_updated'));
    }

    public function destroy(EquipmentUnit $equipmentUnit)
    {
        // Archive, never a hard delete — a unit's history matters even once it's retired.
        $equipmentUnit->update(['is_active' => false]);

        return redirect()->route('equipment-units.index')
            ->with('success', __('equipment.unit_archived'));
    }

    private function pickers(): array
    {
        return [
            'customers'     => Customer::where('status', true)->orderBy('name')->get(),
            'categories'    => EquipmentCategory::where('status', true)->orderBy('name')->get(),
            'subtypes'      => EquipmentSubtype::where('status', true)->orderBy('name')->get(['id', 'name', 'name_en', 'equipment_category_id']),
            'manufacturers' => EquipmentManufacturer::where('status', true)->orderBy('name')->get(),
            'models'        => EquipmentModel::where('status', true)->orderBy('model_number')
                ->get(['id', 'model_number', 'equipment_manufacturer_id', 'equipment_category_id', 'equipment_subtype_id']),
            'reportTemplates' => MaintenanceReportTemplate::where('status', true)->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $unitId = $request->route('equipment_unit')?->id;

        return $request->validate([
            'customer_id'            => ['required', 'exists:customers,id'],
            'equipment_category_id'  => ['required', 'exists:equipment_categories,id'],
            'equipment_subtype_id'   => ['nullable', 'exists:equipment_subtypes,id'],
            'equipment_model_id'     => ['required', 'exists:equipment_models,id'],
            'name'                   => [
                'required', 'string', 'max:150',
                "unique:equipment_units,name,{$unitId},id,customer_id,{$request->input('customer_id')}",
            ],
            'name_en'                => ['nullable', 'string', 'max:150'],
            'serial_number'          => ['nullable', 'string', 'max:150'],
            'location'               => ['nullable', 'string', 'max:255'],
            'project'                => ['nullable', 'string', 'max:150'],
            'status'                 => ['required', 'in:'.implode(',', EquipmentUnit::STATUSES)],
            'notes'                  => ['nullable', 'string'],
            'report_template_id'     => ['nullable', 'exists:maintenance_report_templates,id'],
            'superheat_low'          => ['nullable', 'numeric'],
            'superheat_high'         => ['nullable', 'numeric', 'gt:superheat_low'],
        ]);
    }

    /** Packs the optional superheat-band override inputs into the reading_limits JSON column. */
    private function readingLimits(Request $request): ?array
    {
        if (! $request->filled('superheat_low') || ! $request->filled('superheat_high')) {
            return null;
        }

        return ['superheat' => ['low' => (float) $request->input('superheat_low'), 'high' => (float) $request->input('superheat_high')]];
    }
}
