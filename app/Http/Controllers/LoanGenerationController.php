<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoanGenerationController extends Controller
{
    public function index()
    {
        return view('generate');
    }
}