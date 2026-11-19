<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class MaterialsImport implements OnEachRow, WithHeadingRow, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    public function isEmptyWhen(array $row): bool
    {
        $row = ImportRow::material($row);
        $name = preg_replace('/[\s\x{0640}]+/u', '', $row['name'] ?? '');

        return $row['code'] === null && $row['unit'] === null
            && in_array($name, ['المجموعالنهائي', 'المجموع', 'الإجمالي', 'الاجمالي'], true);
    }

    public function onRow(Row $sheetRow): void
    {
        $raw = $sheetRow->toArray();
        if ($this->isEmptyWhen($raw)) {
            return;
        }

        $row = ImportRow::material($raw);
        $validator = Validator::make($row, $this->rules());
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $attribute => $messages) {
                $this->onFailure(new Failure($sheetRow->getIndex(), $attribute, $messages, $row));
            }
            return;
        }

        $parent = $row['group'] ? $this->findOrCreateCategory($row['group'], null, true) : null;
        $category = $this->findOrCreateCategory($row['category'], $parent?->id, $parent !== null);
        $unit = Unit::where('name', $row['unit'])->orWhere('name_en', $row['unit'])->first()
            ?? Unit::create(['name' => $row['unit'], 'status' => true]);

        Material::create([
            'category_id' => $category->id,
            'unit_id'     => $unit->id,
            'code'        => $row['code'],
            'name'        => $row['name'],
            'name_en'     => $row['name_en'],
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
            'category' => ['required', 'string', 'max:255'],
            'group'    => ['nullable', 'string', 'max:255'],
            'unit'     => ['required', 'string', 'max:255'],
        ];
    }

    private function findOrCreateCategory(string $name, ?int $parentId = null, bool $scopeParent = false): MaterialCategory
    {
        return MaterialCategory::when($scopeParent, fn ($query) => $query->where('parent_id', $parentId))
            ->where(fn ($query) => $query->where('name', $name)->orWhere('name_en', $name))->first()
            ?? MaterialCategory::create(['name' => $name, 'parent_id' => $parentId, 'status' => true]);
    }
}
