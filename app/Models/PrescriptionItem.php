<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'medicine_name',
        'quantity',
        'unit_price',
        'total_price',
        'notes'
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
