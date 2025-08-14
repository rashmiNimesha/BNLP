<?php

namespace App\Events;

use App\Models\Installment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstallmentPaid implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $installment;

    public function __construct(Installment $installment)
    {
        $this->installment = $installment;
    }

    public function broadcastOn()
    {
        return new Channel('loans');
    }

    public function broadcastWith()
    {
        return ['installment' => $this->installment, 'loan_id' => $this->installment->loan_id];
    }
}