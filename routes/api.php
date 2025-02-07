<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\NodelinkController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('add.ngrok.header')->group(function(){
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('jwt.verify')->get('me', [AuthController::class, 'me']);
        Route::middleware('jwt.verify')->post('logout', [AuthController::class, 'logout']);
        Route::middleware('jwt.verify')->post('change-password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{id}', [ProductController::class, 'show']);
    });

    Route::prefix('faq')->group(function (){
        Route::get('{id}', [ProductController::class, 'faqProduct']);
    });

    Route::post('otp/send', [OtpController::class, 'send']);
    Route::post('otp/verify', [OtpController::class, 'verifyOtp']);

    Route::middleware('jwt.verify')->prefix('order')->group(function () {
        Route::post('add-cp', [OrderController::class, 'addCp']);
        Route::post('create', [OrderController::class, 'create']);
        Route::get('created/{id}', [OrderController::class, 'created']);
        Route::get('history', [OrderController::class, 'history']);
        Route::get('detail/{id}', [OrderController::class, 'detail']);
        Route::get('summary/{id}', [OrderController::class, 'summary']);
        Route::post('cancel/{id}', [OrderController::class, 'cancel']);
        Route::post('payment/{id}', [OrderController::class, 'payment']);
        Route::post('snap/callback', [OrderController::class, 'snapCallback']);
        Route::get('cek/{id}', [OrderController::class, 'cekPayment']);
        Route::post('activate', [OrderController::class, 'activate']);
        Route::post('delivered', [OrderController::class, 'delivered']);
    });

    Route::prefix('wilayah')->group(function () {
        Route::get('provinsi', [RegionController::class, 'provinsi']);
        Route::get('kabupaten/{id}', [RegionController::class, 'kabupaten']);
        Route::get('kecamatan/{id}', [RegionController::class, 'kecamatan']);
        Route::get('kelurahan/{id}', [RegionController::class, 'kelurahan']);
    });

    Route::prefix('payment')->group(function () {
        Route::middleware('jwt.verify')->post('gateway', [PaymentController::class, 'gateway']);
        Route::middleware('jwt.verify')->post('midtrans/create', [PaymentController::class, 'createPayment']);
        Route::post('midtrans/notification', [PaymentController::class, 'handleNotification'])->name('payment.notification');
        Route::middleware('jwt.verify')->get('finish', [PaymentController::class, 'finishPayment'])->name('payment.finish');
    });

    Route::middleware('jwt.verify')->prefix('document')->group(function () {
        Route::get('invoice/{id}', [DocumentController::class, 'invoice']);
        Route::get('acceptance-letter/{id}', [DocumentController::class, 'acceptanceLetter']);
        Route::get('activation-letter/{id}', [DocumentController::class, 'activationLetter']);
        Route::post('signature', [DocumentController::class, 'signature']);
        Route::get('billing-invoice/{id}', [DocumentController::class, 'billingInvoice']);
    });

    Route::middleware('jwt.verify')->prefix('deposit')->group(function () {
        Route::get('active', [DepositController::class, 'active']);
        Route::get('used', [DepositController::class, 'used']);
        Route::get('summary', [DepositController::class, 'summary']);
    });

    Route::middleware('jwt.verify')->prefix('billing')->group(function () {
        Route::get('summary', [BillingController::class, 'billingSummary']);
        Route::get('detail/{id}', [BillingController::class, 'billingDetail']);
        Route::post('upload-ppn', [BillingController::class, 'upload']);
        Route::get('nodelink', [NodelinkController::class, 'index']);
    });

    Route::get('test', [BillingController::class, 'test']);
    Route::get('tes-template', function () {
        return view('document.billing-invoice');
    });
});
