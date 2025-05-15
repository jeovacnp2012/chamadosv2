<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
