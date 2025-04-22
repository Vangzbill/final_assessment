<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\ProductController;
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

    Route::get('product', [ProductController::class, 'index'])->name('product.index');

    Route::prefix('admin')->group(function () {
        Route::get('/', [HomeController::class, 'adminLogin'])->name('admin.login');
        Route::get('pesanan', [OrderController::class, 'index'])->name('admin.order');
        Route::get('pesanan/update-status/{id}', [OrderController::class, 'updateStatus'])->name('admin.order.update-status');
        Route::get('pesanan/{id}', [OrderController::class, 'show'])->name('admin.order.show');
        Route::get('tagihan', [BillingController::class, 'index'])->name('admin.billing');
        Route::get('billing/{id}', [BillingController::class, 'show'])->name('admin.billing.show');
        Route::post('generate-billing', [BillingController::class, 'generateBilling'])->name('admin.generate-billing');
    });
});
