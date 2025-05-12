<?php
namespace App\Providers;

use App\Contracts\BelongsToCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class CompanyScopeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Global scope: só aplica se model implementa a interface
        Model::addGlobalScope('company', function (Builder $builder) {
            $model = $builder->getModel();

            // Aplica somente se o model implementa a interface
            if (! $model instanceof BelongsToCompanyInterface) {
                return;
            }

            $user = Auth::user();

            if ($user && !$user->hasRole('Super Admin')) {
                $builder->where($model->getTable() . '.company_id', $user->company_id);
            }
        });

        // Preenchimento automático de company_id na criação
        Model::creating(function (Model $model) {
            if (
                $model instanceof BelongsToCompanyInterface &&
                Auth::check() &&
                ! $model->company_id
            ) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}
