<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Called extends Model
{
    protected $fillable = [
        'user_id',
        'sector_id',
        'supplier_id',
        'patrimony_id',
        'called_type_id',
        'problem',
        'protocol',
        'status',
        'type_maintenance',
        'closing_date',
    ];
    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function patrimony(): BelongsTo
    {
        return $this->belongsTo(Patrimony::class);
    }
    public function calledType(): BelongsTo
    {
        return $this->belongsTo(CalledType::class);
    }
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
