<?php

namespace App\Enums;

enum ItemTypeEnum: string
{
    case PART = 'part';
    case SERVICE = 'service';

    public function label(): string
    {
        return match ($this) {
            self::PART => 'PEÇAS',
            self::SERVICE => 'SERVIÇOS',
        };
    }

    public static function options(): array
    {
        return [
            self::PART->value => self::PART->label(),
            self::SERVICE->value => self::SERVICE->label(),
        ];
    }
}
