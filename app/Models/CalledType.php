<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalledType extends Model
{
    protected $fillable = ['name'];

    public function calleds(): HasMany
    {
        return $this->hasMany(Called::class);
    }
}
