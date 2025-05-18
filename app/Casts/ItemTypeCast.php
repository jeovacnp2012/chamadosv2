<?php

namespace App\Casts;

use App\Enums\ItemTypeEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ItemTypeCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return ItemTypeEnum::tryFrom($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return [
            $key => $value instanceof ItemTypeEnum ? $value->value : $value,
        ];
    }
}
