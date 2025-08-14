<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = ['amount', 'status'];
    protected $casts = [
        'status' => 'string',
    ];

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function totalPaid()
    {
        return $this->installments()->where('status', 'paid')->sum('amount');
    }

    public function installmentsPaidCount()
    {
        return $this->installments()->where('status', 'paid')->count();
    }

    public function nextDueDate()
    {
        $installment = $this->installments()->where('status', 'pending')->orderBy('due_date')->first();
        return $installment 
            ? \Carbon\Carbon::parse($installment->due_date)->setTimezone('Asia/Colombo') 
            : null;
    }

    public function updateStatus()
    {
        $allPaid = $this->installments()->where('status', '!=', 'paid')->count() === 0;
        $this->status = $allPaid ? 'completed' : 'active';
        $this->save();

        if ($allPaid) {
            broadcast(new \App\Events\LoanCompleted($this));
        }
    }
}
