<?php

namespace App\Constants;

trait EnumValuesTrait
{

    /**
     * 获取value内容
     * @return array
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }

    /**
     * 获取value和名称
     * @return array
     */
    public static function valueMessage(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'name' => $case->getMessage(),
            ];
        }, static::cases());
    }
}
