<?php

use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('add.ngrok.header')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('payment-success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/home', function () {
        return view('pages.landing');
    })->name('dashboard');
});
