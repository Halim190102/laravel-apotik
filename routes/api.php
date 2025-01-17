<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\UserChangeController;
use App\Http\Controllers\Main\CartItemController;
use App\Http\Controllers\Main\CategoryController;
use App\Http\Controllers\Main\OrderController;
use App\Http\Controllers\Main\ProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');


    Route::middleware('auth:api')->group(function () {
        Route::resources([
            'products' => ProductController::class,
            'categories' => CategoryController::class,
            'cart_items' => CartItemController::class,
            'orders' => OrderController::class,
        ]);

        Route::post('/categories_delete', [CategoryController::class, 'multiDestroy'])->name('catmultiDestroy');
        Route::post('/products_delete', [ProductController::class, 'multiDestroy'])->name('promultiDestroy');
        Route::post('/cart_items_delete', [CartItemController::class, 'multiDestroy'])->name('carmultiDestroy');
        Route::post('/orders_delete', [OrderController::class, 'multiDestroy'])->name('ordmultiDestroy');

        Route::post('/change_status/{order}', [OrderController::class, 'change_status'])->name('changeStatus');
        Route::get('/cancelled/{order}', [OrderController::class, 'cancelled'])->name('canceled');

        Route::post('/update_password', [UserChangeController::class, 'changeUserPassword'])->name('changeUserPassword');
        Route::post('/update_name', [UserChangeController::class, 'changeName'])->name('changeName');
        Route::post('/update_image', [UserChangeController::class, 'changeImage'])->name('changeImage');

        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });


    Route::get('/auth/{driver}', [SocialLoginController::class, 'toProvider'])->where('driver', 'github|google|facebook');
    Route::get('/callback/{driver}/login', [SocialLoginController::class, 'handleCallback'])->where('driver', 'github|google|facebook');

    Route::get('/success={email}&{token}', [EmailVerificationController::class, 'verifiedSuccess']);
    Route::post('/resend_email', [EmailVerificationController::class, 'resendEmailVerificationLink'])->name('resendEmailVerificationLink');
    Route::post('/check_verify_email', [EmailVerificationController::class, 'checkVerify'])->name('checkVerify');

    Route::post('/send_reset_code', [ResetPasswordController::class, 'sendCodeLink'])->name('sendCodeLink');
    Route::post('/verify_reset_code', [ResetPasswordController::class, 'checkCodeVerify'])->name('checkCodeVerify');
    Route::post('/reset_password', [ResetPasswordController::class, 'resetPassword'])->name('resetPassword');
});
