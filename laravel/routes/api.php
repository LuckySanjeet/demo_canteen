<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});



Route::get('/get-all-orders',[OrderController::class, 'getAllOrderLists'])->middleware(['auth:sanctum','isAdminOrStaff']);


Route::middleware(['auth:sanctum','isAdminOrStaff'])->group(function () {
    // Order routes
    Route::post('/orders', [OrderController::class, 'create']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::get('/orders/{id}', [OrderController::class, 'track']);

    // Payment routes
    Route::post('/payments', [PaymentController::class, 'createPaymentLink']);
    Route::get('/payment/success', [PaymentController::class, 'handleSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'handleCancel'])->name('payment.cancel');
});



Route::prefix('products')->group(function () {

    Route::get('/', [ProductController::class, 'getProductList']);

    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'edit']);
        Route::delete('/{id}', [ProductController::class, 'delete']);
        Route::get('/all', [ProductController::class, 'getProducts']);
    });
});
