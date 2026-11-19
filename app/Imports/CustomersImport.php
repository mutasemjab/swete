<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    /** Computed once per import, not per row — Customer::nextCode() re-queries count() and would
     *  hand out the SAME code to every row in one batch otherwise (rows aren't saved one-by-one
     *  before the next model() call runs). */
    private int $nextSeq;

    public function __construct(private int $createdBy)
    {
        $this->nextSeq = (int) Customer::where('code', 'like', 'CUST-%')->pluck('code')
            ->map(fn ($code) => ctype_digit(substr($code, 5)) ? (int) substr($code, 5) : 0)->max() + 1;
    }

    public function prepareForValidation(array $row, int $index): array
    {
        return ImportRow::party($row);
    }

    public function model(array $row)
    {
        return new Customer([
            'code'            => sprintf('CUST-%05d', $this->nextSeq++),
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
