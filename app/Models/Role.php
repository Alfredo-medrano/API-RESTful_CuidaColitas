<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    //
    public $timestamps = true;
    protected $fillable = ['name', 'display_name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
