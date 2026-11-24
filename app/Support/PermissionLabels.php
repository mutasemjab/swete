<?php

namespace App\Support;

use Illuminate\Support\Facades\Lang;

class PermissionLabels
{
    public static function permission(string $name): string
    {
        return self::translate($name, $name);
    }

    public static function group(string $name): string
    {
        return self::translate('groups.'.$name, $name);
    }

    public static function role(string $name): string
    {
        return self::translate('roles.'.$name, $name);
    }

    private static function translate(string $key, string $fallback): string
    {
        return Lang::has('permissions.'.$key) ? __('permissions.'.$key) : $fallback;
    }
}
