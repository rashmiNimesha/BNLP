<?php

namespace App\Console\Commands;

use App\Jobs\ProcessInstallmentPayment;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessDueInstallments extends Command
{
    protected $signature = 'installments:process';
    protected $description = 'Process due installments';

    public function handle()
    {
        $dueInstallments = Installment::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now())
            ->get();

        foreach ($dueInstallments as $installment) {
            ProcessInstallmentPayment::dispatch($installment);
        }

        $this->info('Due installments queued for processing.');
    }
}