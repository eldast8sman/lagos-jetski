<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\AuthController as ControllersAuthController;
use App\Services\G5PosService;
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
        Route::get('/refresh-token', 'refresh_token')->name('admin.refreshToken');
    });

    Route::middleware('auth:admin-api')->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::get('/me', 'me')->name('admin.me');
            Route::post('/account-details', 'update_account_details')->name('admin.updateAccountDetails');
            Route::post('/me', 'update')->name('admin.profileUpdate');
            Route::post('/change-password', 'change_password')->name('admin.passwordChange');
        });

        Route::controller(AdminController::class)->group(function(){
            Route::get('/admins', 'index')->name('admin.admins.index');
            Route::post('/admins', 'store')->name('admin.admins.store');
            Route::get('/admins/{uuid}', 'show')->name('admin.admins.show');
            Route::post('/admins/{uuid}', 'update')->name('admin.admin.update');
            Route::delete('/admins/{uuid}', 'destroy')->name('admin.admin.delete');
        });

        Route::controller(MembershipController::class)->prefix('members')->group(function(){
            Route::get('/', 'index')->name('admin.members.index');
        });
    });
});

Route::prefix('user')->group(function(){
    Route::controller(ControllersAuthController::class)->group(function(){
        Route::post('/forgot-password', 'forgot_password')->name('user.forgotPassword');
        Route::post('/reset-password', 'reset_password')->name('user.resetPassword');

        Route::get('/fetch-by-token', 'fetch_token')->name('user.fetchByToken');
        Route::post('/verify-email', 'activate_account')->name('user.verifyEmail');

        Route::post('/login', 'login')->name('user.login');

        Route::get('/refresh-token', 'refresh_token')->name('user.refreshToken');
    });
});

Route::get('/g5-login', [G5PosService::class, 'login']);
Route::get('/g5-members', [MembershipController::class, 'store_g5_members']);
