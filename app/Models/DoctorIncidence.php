<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorIncidence extends Model
{
    protected $fillable = [
        'doctor_id',
        'incidence_id',

    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function incidence()
    {
        return $this->belongsTo(Incidence::class);
    }
}
