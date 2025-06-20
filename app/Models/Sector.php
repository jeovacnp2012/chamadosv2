<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'departament_id',
        'address_id',
        'extension',
        'cell_phone',
        'responsible',
        'email',
        'is_active',
    ];

    public function departament(): BelongsTo
    {
        return $this->belongsTo(Departament::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function patrimonies(): HasMany
    {
        return $this->hasMany(Patrimony::class);
    }

    /**
     * Relacionamento many-to-many com usuários
     * CORRIGIDO: especificando o nome correto da tabela pivot
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sector_user', 'sector_id', 'user_id');
    }
}
