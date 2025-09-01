<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CensusData extends Model
{
    protected $fillable = [
        'census_id',
        'data',
        'observation',
        'is_foreign',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function Census()
    {
        return $this->belongsTo(Census::class);
    }
}
