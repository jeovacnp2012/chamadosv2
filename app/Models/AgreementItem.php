<?php

namespace App\Models;

use App\Enums\ItemTypeEnum;
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
    protected $casts = [
        'type' => ItemTypeEnum::class,
    ];

    public function priceAgreement(): BelongsTo
    {
        return $this->belongsTo(PriceAgreement::class);
    }
}
