<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id','address_line1','address_line2','city','state','zip','country','is_primary',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
