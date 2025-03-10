<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('add.ngrok.header')->group(function () {
    Route::get('/', function () {
        return view('pages.landing');
    })->name('dashboard');

    Route::get('payment-success', [PaymentController::class, 'success'])->name('payment.success');

    Route::prefix('auth')->group(function () {
        Route::get('/register', [AuthController::class, 'register'])->name('register');

        Route::get('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::get('chatbot', [ChatbotController::class, 'index'])->name('chatbot');
});
