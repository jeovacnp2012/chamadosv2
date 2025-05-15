<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait ChecksResourcePermission
{
    public static function canViewAny(): bool
    {
        return self::checkPermission('view');
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
