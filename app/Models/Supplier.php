<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model implements BelongsToCompanyInterface
{
    protected $fillable = [
        'corporate_name',
        'trade_name',
        'cnpj',
        'state_registration',
        'address_id',
        'cell_phone',
        'email',
        'is_active',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function atas(): HasMany
    {
        return $this->hasMany(PriceAgreement::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
