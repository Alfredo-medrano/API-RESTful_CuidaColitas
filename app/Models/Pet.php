<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Pet extends Model
{
    //
    protected $fillable = [
        'name',
        'species',
        'breed',
        'birth_date',
        'sex',
        'color',
        'is_active',
        'photo_path',
    ];


    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
