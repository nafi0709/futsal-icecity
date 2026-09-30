<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'snap_token',
        'qr_code_url',
        'payment_proof_path',
        'payment_proof_url',
        'payment_notes',
        'transaction_id',
        'status',
        'paid_at',
        'proof_uploaded_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'proof_uploaded_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getProofUrlAttribute()
    {
        if ($this->attributes['payment_proof_url']) {
            return $this->attributes['payment_proof_url'];
        }
        
        if ($this->attributes['payment_proof_path']) {
            return asset('storage/' . $this->attributes['payment_proof_path']);
        }
        
        return null;
    }
}