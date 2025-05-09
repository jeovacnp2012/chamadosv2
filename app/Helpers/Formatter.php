<?php

namespace App\Helpers;

class Formatter
{
    public static function phone(?string $value): ?string
    {
        if (!$value) return null;
        $length = strlen($value);

        if ($length === 11) {
            return sprintf('(%s) %s-%s',
                substr($value, 0, 2),
                substr($value, 2, 5),
                substr($value, 7)
            );
        }

        if ($length === 10) {
            return sprintf('(%s) %s-%s',
                substr($value, 0, 2),
                substr($value, 2, 4),
                substr($value, 6)
            );
        }

        return $value;
    }

    public static function cnpj(?string $value): ?string
    {
        if (!$value || strlen($value) !== 14) return $value;

        return preg_replace(
            '/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',
            '$1.$2.$3/$4-$5',
            $value
        );
    }

    public static function cep(?string $value): ?string
    {
        if (!$value || strlen($value) !== 8) return $value;

        return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $value);
    }
    public static function isValidCnpj(?string $cnpj): bool
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        for ($t = 12; $t < 14; $t++) {
            $d = 0;
            $c = 0;

            for ($m = 0, $i = $t - 7; $m < $t; $m++, $i--) {
                $i = $i < 2 ? 9 : $i;
                $d += $cnpj[$m] * $i;
            }

            $c = ((10 * $d) % 11) % 10;

            if ($cnpj[$t] != $c) {
                return false;
            }
        }

        return true;
    }
    public static function isValidPhone(?string $phone): bool
    {
        $phone = preg_replace('/\D/', '', $phone);
        return in_array(strlen($phone), [10, 11]);
    }
    public static function isValidCep(?string $cep): bool
    {
        return preg_match('/^\d{8}$/', $cep) === 1;
    }


}
