<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::creating(function ($model) {
            if (!Auth::check()) {
                return;
            }
            $user = Auth::user();
            // Super Admin pode criar registros para qualquer empresa
            if ($user->hasRole('Super Admin')) {
                return;
            }
            // Usuário comum: se company_id ainda não foi atribuído manualmente
            if (! $model->company_id) {
                $model->company_id = $user->company_id;
            }
        });
    }
}
