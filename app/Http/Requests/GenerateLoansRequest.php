<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLoansRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // Public for demo
    }

    public function rules()
    {
        return [
            'loan_amount' => 'required|numeric|min:1',
            'number_of_loans' => 'required|integer|min:1',
            'installments_per_loan' => 'integer|min:1',
            'installment_period_minutes' => 'required|integer|min:1',
        ];
    }
}