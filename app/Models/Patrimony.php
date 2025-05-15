<?php

namespace App\Models;

use App\Contracts\BelongsToCompanyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patrimony extends Model implements BelongsToCompanyInterface
{
    protected $fillable = [
        'sector_id',
        'tag',
        'description',
        'observation',
        'image_path',
        'purchase_date',
        'purchase_value',
        'write_off_reason',
        'write_off_date',
        'has_report',
        'report_date',
        'type',
        'acquisition_type',
        'acquisition_value',
        'acquisition_date',
        'current_value',
        'is_active',
    ];
    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }
}
