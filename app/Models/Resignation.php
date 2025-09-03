<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    protected $fillable = [
        'doctor_id',
        'reason',
        'resignation_letter',
        'document',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
