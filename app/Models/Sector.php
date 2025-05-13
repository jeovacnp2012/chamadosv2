<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'address_id',
        'extension',
        'cell_phone',
        'responsible',
        'email',
        'is_active',
    ];
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
