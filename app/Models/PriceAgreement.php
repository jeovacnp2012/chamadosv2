<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceAgreement extends Model implements BelongsToCompanyInterface
{
    use BelongsToCompany;
    protected $fillable = [
        'number',
        'year',
        'signature_date',
        'valid_until',
        'object',
        'executor_id',
        'company_id',
        'supplier_id',
        'is_active',
    ];

    public function executor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AgreementItem::class);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
