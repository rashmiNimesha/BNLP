<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;
    protected $fillable = ['loan_id', 'amount', 'due_date', 'status', 'paid_at'];
    protected $casts = [
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'status' => 'string',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function paymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class);
    }
}
