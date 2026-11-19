<?php

namespace App\Imports;

use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ImportRow
{
    public static function normalize(array $row, array $aliases): array
    {
        $normalized = [];

        foreach ($aliases as $field => $headings) {
            $normalized[$field] = null;
            foreach (array_merge([$field], $headings) as $heading) {
                $key = HeadingRowFormatter::format([$heading])[0];
                if (array_key_exists($key, $row) && $row[$key] !== null) {
                    $value = preg_replace('/^[\s\x{FEFF}]+|[\s\x{FEFF}]+$/u', '', (string) $row[$key]);
                    $normalized[$field] = $value === '' ? null : $value;
                    break;
                }
            }
        }

        return $normalized;
    }

    public static function party(array $row): array
    {
        return self::normalize($row, [
            'name' => ['الاسم', 'اسم العميل', 'اسم المورد'],
            'name_en' => ['الاسم بالانجليزي', 'الاسم بالإنجليزية'],
            'phone' => ['رقم الهاتف', 'الهاتف'],
            'email' => ['البريد الإلكتروني', 'البريد الالكتروني'],
            'address' => ['المنطقة', 'العنوان'],
            'tax_number' => ['الرقم الضريبي'],
            'opening_balance' => ['الرصيد الافتتاحي'],
        ]);
    }

    public static function material(array $row): array
    {
        return self::normalize($row, [
            'code' => ['رمزالصنف', 'رمز الصنف'],
            'name' => ['اسم الصنف', 'اسم المادة'],
            'name_en' => ['الاسم بالإنجليزية'],
            'category' => ['التصنيف', 'الفئة'],
            'group' => ['المجموعة'],
            'unit' => ['الوحدة'],
        ]);
    }
}
