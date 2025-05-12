<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model implements BelongsToCompanyInterface
{
    protected $fillable = [
        'postal_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'company_id',
    ];
    public function getFormattedAddressAttribute(): string
    {
        return "{$this->street}" .
            ($this->number ? ", {$this->number}" : "") .
            " - {$this->neighborhood} - {$this->city}/{$this->state}";
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

}
