<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /** Computed once per import, not per row — see CustomersImport's identical note. */
    private int $nextSeq;

    public function __construct(private int $createdBy)
    {
        $this->nextSeq = Supplier::count() + 1;
    }

    public function model(array $row)
    {
        return new Supplier([
            'code'            => sprintf('SUPP-%05d', $this->nextSeq++),
            'name'            => $row['name'],
            'name_en'         => $row['name_en'] ?? null,
            'phone'           => $row['phone'] ?? null,
            'email'           => $row['email'] ?? null,
            'address'         => $row['address'] ?? null,
            'tax_number'      => $row['tax_number'] ?? null,
            'opening_balance' => $row['opening_balance'] ?? 0,
            'status'          => true,
            'created_by'      => $this->createdBy,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:150'],
            'name_en'         => ['nullable', 'string', 'max:150'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'email'           => ['nullable', 'email', 'max:150'],
            'address'         => ['nullable', 'string', 'max:255'],
            'tax_number'      => ['nullable', 'string', 'max:50'],
            'opening_balance' => ['nullable', 'numeric'],
        ];
    }
}
