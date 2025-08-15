<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanGenerationController;
use App\Http\Controllers\LoanDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::get('/generate', [LoanGenerationController::class, 'index'])->name('generate');
Route::get('/dashboard', [LoanDashboardController::class, 'index'])->name('dashboard');