<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function(){
    Route::controller(AuthController::class)->group(function(){
        Route::post('/first-admin', 'store')->name('admin.firstStore');
        Route::get('/find-by-verification-token/{token}', 'fetch_by_verification_token')->name('admin.findByVerificationToken');
        Route::post('/activate-account', 'activate_account')->name('admin.activate_account');
        Route::post('/forgot-password', 'forgot_password')->name('admin.forgot_password');
        Route::post('/reset-password', 'reset_password')->name('admin.resetPassword');
        Route::post('/login', 'login')->name('admin.login');
    });
});
