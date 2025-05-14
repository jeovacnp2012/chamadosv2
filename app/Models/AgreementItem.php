<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementItem extends Model
{
    protected $fillable = [
        'ata_id',
        'code',
        'description',
        'quantity',
        'unit_price',
        'unit',
        'type',
        'is_active',
    ];

    public function ata(): BelongsTo
    {
        return $this->belongsTo(PriceAgreement::class);
    }
}
