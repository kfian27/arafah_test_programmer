<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionMedicine extends Model
{
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'quantity',
        'instructions',
        'unit_price',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
