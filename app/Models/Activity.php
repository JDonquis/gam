<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'data',
        'user_id',
        'type_activity_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function typeActivity(){
        return $this->belongsTo(TypeActivity::class);
    }
}
