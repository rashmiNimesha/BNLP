<?php

namespace App\Jobs;

use App\Events\InstallmentPaid;
use App\Models\Installment;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessInstallmentPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $installment;

    public function __construct(Installment $installment)
    {
        $this->installment = $installment;
    }

    public function handle()
    {
        if ($this->installment->status !== 'pending') {
            Log::info('Installment already processed: ' . $this->installment->id);
            return;  // Idempotency
        }

        try {
            $this->installment->update([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            PaymentTransaction::create([
                'installment_id' => $this->installment->id,
                'amount' => $this->installment->amount,
                'processed_at' => Carbon::now(),
                'status' => 'success',
            ]);

            broadcast(new InstallmentPaid($this->installment));

            $this->installment->loan->updateStatus();
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());
            PaymentTransaction::create([
                'installment_id' => $this->installment->id,
                'amount' => $this->installment->amount,
                'processed_at' => Carbon::now(),
                'status' => 'failed',
            ]);
        }
    }
}