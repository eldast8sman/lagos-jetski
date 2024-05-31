<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function(){
    Route::controller(AuthController::class)->group(function(){
        Route::post('/first-admin', 'store')->name('admin.firstStore');
        Route::post('/activate-account', 'activate_account')->name('admin.activate_account');
    });
});
