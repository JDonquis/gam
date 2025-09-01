<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Census extends Model
{
    protected $fillable = [
        'title',
        'file',
        'type',
        'size',
        'user_id',
        'percentage',
        'is_completed',
        'configuration_id',
        'type_document_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function configuration()
    {
        return $this->belongsTo(Configuration::class);
    }
}
