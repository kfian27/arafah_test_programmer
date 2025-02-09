<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'medical_record_number',
        'birth_date',
        'gender',
        'address',
        'phone_number'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }
}
