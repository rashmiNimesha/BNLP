<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class LoanGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan->load('installments'); 
    }

    public function broadcastOn()
    {
        return new Channel('loans');
    }

    public function broadcastWith()
    {
        return ['loan' => $this->loan];
    }
}