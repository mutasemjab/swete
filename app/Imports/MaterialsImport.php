<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MaterialsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Material([
            'category_id' => $this->findCategory($row['category'])->id,
            'unit_id'     => $this->findUnit($row['unit'])->id,
            'code'        => $row['code'],
            'name'        => $row['name'],
            'name_en'     => $row['name_en'] ?? null,
            'status'      => true,
            'is_draft'    => false,
        ]);
    }

    public function rules(): array
    {
        return [
            'code'     => ['required', 'string', 'max:50', 'unique:materials,code'],
            'name'     => ['required', 'string', 'max:150'],
            'name_en'  => ['nullable', 'string', 'max:150'],
            'category' => ['required', 'string', function ($attribute, $value, $fail) {
                if (! $this->findCategory($value)) {
                    $fail(__('warehouse.import_category_not_found', ['name' => $value]));
                }
            }],
            'unit' => ['required', 'string', function ($attribute, $value, $fail) {
                if (! $this->findUnit($value)) {
                    $fail(__('warehouse.import_unit_not_found', ['name' => $value]));
                }
            }],
        ];
    }

    /** Matched by Arabic or English name, so the Excel column can be filled in either language. */
    private function findCategory(?string $name): ?MaterialCategory
    {
        $name = trim((string) $name);

        return MaterialCategory::where('name', $name)->orWhere('name_en', $name)->first();
    }

    private function findUnit(?string $name): ?Unit
    {
        $name = trim((string) $name);

        return Unit::where('name', $name)->orWhere('name_en', $name)->first();
    }
}
