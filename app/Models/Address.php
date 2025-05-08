<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'postal_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
    ];
    public function getFormattedAddressAttribute(): string
    {
        return "{$this->street}" .
            ($this->number ? ", {$this->number}" : "") .
            " - {$this->neighborhood} - {$this->city}/{$this->state}";
    }
}
