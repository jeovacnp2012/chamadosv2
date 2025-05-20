<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    protected $fillable = [
        'called_id',
        'user_id',
        'message',
        'attachment_path',
    ];
    public function called(): BelongsTo
    {
        return $this->belongsTo(Called::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
