<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'name',
        'structure',
    ];

    public $casts = [
        'structure' => 'array',
    ];
}
