<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    protected $fillable = [
        'patient_name',
        'patient_address',
        'examination_time',
        'height',
        'weight',
        'systole',
        'diastole',
        'heart_rate',
        'respiration_rate',
        'temperature',
        'examination_result',
        'examination_file',
        'doctor_id'
    ];

    protected $casts = [
        'examination_time' => 'datetime'
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
}
