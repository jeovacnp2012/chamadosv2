<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departament extends Model implements BelongsToCompanyInterface
{
    protected $fillable = [
        'company_id',
        'address_id',
        'name',
        'contact_person',
        'cell_phone',
        'extension',
        'email',
        'is_active',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function sectors(): HasMany
    {
        return $this->hasMany(Sector::class);
    }

    /**
     * Relacionamento many-to-many com usuários
     * CORRIGIDO: especificando o nome correto da tabela pivot
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'departament_user', 'departament_id', 'user_id');
    }
}
