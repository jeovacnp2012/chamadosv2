<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    public static function bootHasCompanyScope(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $user = Auth::user();
            if ($user && !$user->hasRole('Super Admin')) {
                $builder->where('company_id', $user->company_id);
            }
        });
    }

    public function scopeOfUserCompany(Builder $query): Builder
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('Super Admin')) {
            return $query->where('company_id', $user->company_id);
        }

        return $query;
    }
}
