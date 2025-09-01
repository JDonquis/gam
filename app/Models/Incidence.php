<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    protected $fillable = [
        'reason',
        'doctor_id',
        'census_data_id',
        'status_resolve'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status_resolve' => 'boolean',
    ];

    /**
     * Get the doctor associated with the incidence.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the census data associated with the incidence.
     */
    public function censusData()
    {
        return $this->belongsTo(CensusData::class);
    }
}
