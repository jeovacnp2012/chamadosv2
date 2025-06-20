<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ChecksResourcePermission
{
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        // Super Admin sempre vê
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        // Admin vê se tem departamentos vinculados
        if ($user->hasRole('Admin') && $user->departaments()->exists()) {
            return true;
        }
        // Executor vê se tem supplier_id
        if ($user->hasRole('Executor') && $user->supplier_id) {
            return true;
        }
        // Outros perfis: só vêem se tem departamentos vinculados
        return $user->departaments()->exists();
    }

    public static function canCreate(): bool
    {
        return self::checkPermission('create');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return self::checkPermission('update');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return self::checkPermission('delete');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        $name = Str::of(class_basename(static::class))
            ->replace('Resource', '')
            ->kebab();

        return auth()->user()?->can("view {$name}");
    }

    protected static function checkPermission(string $action): bool
    {
        $resource = class_basename(static::class);
        $name = str($resource)->replace('Resource', '')->kebab();

        $user = auth()->user();
        $perm = "$action $name";

        return $user && (
                $user->hasRole('Super Admin') || $user->can($perm)
            );
    }
}
