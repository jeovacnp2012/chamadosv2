<?php

namespace App\Support;

use Closure;

class ValidationRules
{
    protected static function isCreateContext(): bool
    {
        return str(request()->route()?->getName())->contains('.create');
    }
    public static function formatCnpj(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = preg_replace('/\D/', '', $value);

        return preg_replace('/(\\d{2})(\\d{3})(\\d{3})(\\d{4})(\\d{2})/', '$1.$2.$3/$4-$5', $value);
    }
    public static function formatCpf(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = preg_replace('/\D/', '', $value);

        return preg_replace('/(\\d{3})(\\d{3})(\\d{3})(\\d{2})/', '$1.$2.$3-$4', $value);
    }
    public static function formatCep(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = preg_replace('/\D/', '', $value);

        return preg_replace('/(\\d{5})(\\d{3})/', '$1-$2', $value);
    }
    public static function formatPhone(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $value);
        }

        if (strlen($value) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $value);
        }

        return $value; // se não tiver 10 ou 11 dígitos, retorna como está
    }
    public static function cnpj(): Closure
    {
        return function ($attribute, $value, $fail) {
            $value = preg_replace('/\D/', '', $value);

            if (strlen($value) != 14 || preg_match('/(\d)\1{13}/', $value)) {
                return $fail('O CNPJ informado é inválido.');
            }

            for ($t = 12; $t < 14; $t++) {
                $d = 0;
                for ($m = 0, $i = $t - 7; $m < $t; $m++, $i--) {
                    $i = $i < 2 ? 9 : $i;
                    $d += $value[$m] * $i;
                }

                $digit = ((10 * $d) % 11) % 10;
                if ((int) $value[$t] !== $digit) {
                    return $fail('O CNPJ informado é inválido.');
                }
            }

            if (self::isCreateContext()) {
                $exists = \App\Models\Company::where('cnpj', $value)->exists();

                if ($exists) {
                    $fail('Este CNPJ já está cadastrado. Verifique o cadastro existente.');
                }
            }
        };
    }

    public static function phone(): Closure
    {
        return function ($attribute, $value, $fail) {
            $value = preg_replace('/\D/', '', $value);
            $length = strlen($value);

            if (!in_array($length, [10, 11])) {
                return $fail('O número de telefone deve conter 10 ou 11 dígitos.');
            }

            if (self::isCreateContext()) {
                $exists = \App\Models\Company::where('phone', $value)->exists();

                if ($exists) {
                    $fail('Este telefone já está cadastrado.');
                }
            }
        };
    }

    public static function cep(): Closure
    {
        return function ($attribute, $value, $fail) {
            $value = preg_replace('/\D/', '', $value);

            if (!preg_match('/^\d{8}$/', $value)) {
                $fail('O CEP informado é inválido.');
            }
        };
    }

}
