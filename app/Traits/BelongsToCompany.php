<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && !$model->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}
