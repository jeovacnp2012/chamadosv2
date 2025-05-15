<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'corporate_name',
        'trade_name',
        'state_registration',
        'cnpj',
        'phone',
        'email',
        'is_active',
        'address_id',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
    public function departaments(): HasMany
    {
        return $this->hasMany(Departament::class);
    }
}
