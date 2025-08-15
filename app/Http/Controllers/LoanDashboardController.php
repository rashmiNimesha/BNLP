<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanDashboardController extends Controller
{
    public function index()
    {
        $loans = Loan::with('installments')->get();
        return view('dashboard', compact('loans'));
    }
}