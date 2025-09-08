<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'telegram_id',
        'phone',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
    ];
}
