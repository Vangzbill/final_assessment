<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\ChatbotController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::middleware('add.ngrok.header')->group(function () {
    Route::get('/', function () {
        return view('pages.landing');
    })->name('dashboard');

    Route::get('payment-success', [PaymentController::class, 'success'])->name('payment.success');

    Route::prefix('auth')->group(function () {
        Route::get('/register', [AuthController::class, 'register'])->name('register');
        Route::post('submit-register', [AuthController::class, 'submitRegister'])->name('submit.register');

        Route::get('/login', [AuthController::class, 'login'])->name('login');
        Route::post('submit-login', [AuthController::class, 'submitLogin'])->name('submit.login');
        Route::post('/logout', function () {
            Session::forget('jwt_token');
            Session::forget('username');
            return back()->with('success', 'Logout Berhasil!');
        })->name('logout');
    });

    Route::get('chatbot', [ChatbotController::class, 'index'])->name('chatbot');

    Route::get('product', [ProductController::class, 'index'])->name('product.index');
    Route::get('admin', [HomeController::class, 'adminLogin'])->name('admin.login');
    Route::post('admin/submit-login', [HomeController::class, 'adminSubmitLogin'])->name('admin.submit.login');
    Route::post('admin/logout', [HomeController::class, 'logout'])->name('admin.logout');
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('pesanan', [OrderController::class, 'index'])->name('admin.order');
        Route::get('pesanan/update-status/{id}', [OrderController::class, 'updateStatus'])->name('admin.order.update-status');
        Route::get('pesanan/{id}', [OrderController::class, 'show'])->name('admin.order.show');
        Route::get('tagihan', [BillingController::class, 'index'])->name('admin.billing');
        Route::get('billing/{id}', [BillingController::class, 'show'])->name('admin.billing.show');
        Route::post('generate-billing', [BillingController::class, 'generateBilling'])->name('admin.generate-billing');
    });
});
