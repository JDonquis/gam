<?php

namespace App\Models;

use App\Enums\DoctorStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'data',
        'ci',
        'status',
        'is_foreign',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public $fieldsRequired = [
        'ci',
        'start_date',
        'end_date',
    ];

    protected $appends = ['status_name'];


    public function getStatusNameAttribute(): string
    {
        return DoctorStatusEnum::from($this->status)->label();
    }

    public function course()
    {
        return $this->hasOne(Courses::class);
    }
}
