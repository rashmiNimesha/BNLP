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

    /**
     * Calculate due date for an installment.
     * @param \DateTime|string $baseDate
     * @param int $periodMinutes
     * @return \Carbon\Carbon
     */
    public static function calculateDueDate($baseDate, $periodMinutes)
    {
        return \Carbon\Carbon::parse($baseDate)->addMinutes($periodMinutes);
    }
}
