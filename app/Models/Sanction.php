<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    protected $table = 'sanctions';

    protected $fillable = [
        'doctor_id',
        'incidence_id',
        'start_date',
        'end_date',
        'reason',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function Incidence()
    {
        return $this->belongsTo(Incidence::class);
    }
}
