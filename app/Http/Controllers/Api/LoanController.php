<?php

namespace App\Http\Controllers\Api;

use App\Events\LoanGenerated;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateLoansRequest;
use App\Models\Loan;
use App\Models\Installment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    public function generate(GenerateLoansRequest $request)
    {
        $loanAmount = $request->loan_amount;
        $numLoans = $request->number_of_loans;
        $installmentsPerLoan = $request->installments_per_loan ?? 4;
        $periodMinutes = $request->installment_period_minutes;

        $generatedLoans = [];

        for ($i = 0; $i < $numLoans; $i++) {
            $loan = Loan::create([
                'amount' => $loanAmount,
                'status' => 'active',
            ]);

            $installmentAmount = $loanAmount / $installmentsPerLoan;
            $currentDueDate = Carbon::now();

            for ($j = 0; $j < $installmentsPerLoan; $j++) {
                Installment::create([
                    'loan_id' => $loan->id,
                    'amount' => $installmentAmount,
                    'due_date' => $currentDueDate,
                    'status' => 'pending',
                ]);
                $currentDueDate = $currentDueDate->addMinutes($periodMinutes);
            }

            $loan->load('installments');

            broadcast(new LoanGenerated($loan));
            Log::info('LoanGenerated broadcast', ['loan_id' => $loan->id]);

            $generatedLoans[] = $loan;
        }

        return response()->json(['message' => 'Loans generated successfully', 'loans' => $generatedLoans], 201);
    }
}