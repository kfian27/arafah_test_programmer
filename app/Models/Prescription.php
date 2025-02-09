<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'examination_id',
        'status',
        'total_amount',
        'processed_at',
        'pharmacist_id'
    ];

    protected $casts = [
        'processed_at' => 'datetime'
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }

    public function medicines()
    {
        return $this->hasMany(PrescriptionMedicine::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
