<?php

use App\Http\Controllers\Api\AuthController;
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

Route::post('otp/send', [OtpController::class, 'send']);
Route::post('otp/verify', [OtpController::class, 'verifyOtp']);

Route::middleware('jwt.verify')->prefix('order')->group(function () {
    Route::post('add-cp', [OrderController::class, 'addCp']);
    Route::post('create', [OrderController::class, 'create']);
    Route::get('created/{id}', [OrderController::class, 'created']);
    Route::get('history', [OrderController::class, 'history']);
    Route::get('detail/{id}', [OrderController::class, 'detail']);
    Route::post('cancel/{id}', [OrderController::class, 'cancel']);
    Route::post('payment/{id}', [OrderController::class, 'payment']);
    Route::post('snap/callback', [OrderController::class, 'snapCallback']);
});

Route::prefix('wilayah')->group(function () {
    Route::get('provinsi', [RegionController::class, 'provinsi']);
    Route::get('kabupaten/{id}', [RegionController::class, 'kabupaten']);
    Route::get('kecamatan/{id}', [RegionController::class, 'kecamatan']);
    Route::get('kelurahan/{id}', [RegionController::class, 'kelurahan']);
});

Route::prefix('payment')->group(function () {
    Route::middleware('jwt.verify')->post('midtrans/create', [PaymentController::class, 'createPayment']);
    Route::post('midtrans/notification', [PaymentController::class, 'handleNotification'])->name('payment.notification');
    Route::middleware('jwt.verify')->get('finish', [PaymentController::class, 'finishPayment'])->name('payment.finish');
});
