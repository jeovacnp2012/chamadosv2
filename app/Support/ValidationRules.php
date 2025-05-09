<?php

namespace App\Support;

use Closure;

class ValidationRules
{
    // ───── Validações ─────

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
            // Validação de unicidade
            $recordId = request()->route('record')?->id ?? null;
            $exists = \App\Models\Company::where('cnpj', $value)
                ->when($recordId, fn($query) => $query->where('id', '!=', $recordId))
                ->exists();

            if ($exists) {
                $fail('Este CNPJ já está cadastrado. Verifique o cadastro existente.');
            }
        };
    }

    public static function phone(): Closure
    {
        return function ($attribute, $value, $fail) {
            $value = preg_replace('/\D/', '', $value);
            $length = strlen($value);
            if (!in_array($length, [10, 11])) {
                $fail('O número de telefone deve conter 10 ou 11 dígitos.');
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

    // ───── Formatações ─────

    public static function formatCnpj(?string $value): ?string
    {
        if (!$value || strlen($value) !== 14) return $value;

        return preg_replace(
            '/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',
            '$1.$2.$3/$4-$5',
            $value
        );
    }

    public static function formatPhone(?string $value): ?string
    {
        if (!$value) return null;
        $value = preg_replace('/\D/', '', $value);
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

    public static function formatCep(?string $value): ?string
    {
        if (!$value || strlen($value) !== 8) return $value;

        return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $value);
    }
}
