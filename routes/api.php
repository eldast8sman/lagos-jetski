<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AuthController as ControllersAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController as ControllersEventController;
use App\Http\Controllers\MembershipController as ControllersMembershipController;
use App\Http\Controllers\MenuController as ControllersMenuController;
use App\Http\Controllers\NotificationImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RelativeController;
use App\Services\G5PosService;
use App\Services\SparkleService;
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

    Route::get('/notification-images', [NotificationImageController::class, 'index'])->name('notification-images');

    Route::middleware('auth:admin-api')->group(function(){
        Route::controller(AdminDashboardController::class)->group(function(){
            Route::get('/dashboard', 'index');
        });

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
            Route::get('/{user}/verification-resend', 'resend_activation_link')->name('admin.members.verificationLinkResend');
        });

        Route::prefix('orders')->group(function(){
            Route::controller(MenuController::class)->prefix('menu')->group(function(){
                Route::get('/', 'index')->name('admin.menu.index');
                Route::get('/{id}', 'show')->name('admin.menu.show');
                Route::get('/search', 'search')->name('admin.menu.search');
                Route::get('/{id}/modifiers', 'modifiers')->name('admin.menu.modifiers');
            });
            Route::controller(AdminOrderController::class)->group(function(){
                Route::get('/', 'index')->name('admin.orders.index');
                Route::get('/past', 'past_orders')->name('admin.orders.past');
                Route::get('/search', 'search')->name('admin.orders.search');
                Route::post('/', 'store')->name('admin.orders.store');
            });
        });

        Route::prefix('bookings')->controller(AdminBookingController::class)->group(function(){
            Route::get('/', 'index')->name('admin.booking.index');
            Route::get('/past', 'pastBookings')->name('admin.booking.past');
        });

        Route::prefix('events')->controller(EventController::class)->group(function(){
            Route::post('/', 'store')->name('admin.event.store');
            Route::get('/', 'index')->name('admin.event.index');
            Route::get('/past', 'past_events')->name('/admin.events.past');
            Route::post('/{uuid}', 'update')->name('admin.event.update');
            Route::delete('/{uuid}', 'destroy')->name('admin.event.delete');
        });

        Route::prefix('announcements')->controller(AnnouncementController::class)->group(function(){
            Route::post('/', 'store')->name('admin.anouncement.store');
            Route::get('/', 'index')->name('admin.announcement.index');
            Route::get('/{uuid}', 'show')->name('admin.announcement.show');
        });

        Route::prefix('/ads')->controller(AdsController::class)->group(function(){
            Route::post('/', 'store')->name('admin.ads.store');
            Route::get('/', 'index')->name('admin.ads.index');
            Route::get('/{uuid}', 'show')->name('admin.ads.update');
            Route::post('/{uuid}', 'update')->name('admin.ads.update');
            Route::delete('/{uuid}', 'destroy')->name('admin.ads.delete');
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

        Route::post('resend-otp', 'resend_otp')->name('user.OPTResend');
    });

    Route::middleware('auth:user-api')->group(function(){
        Route::prefix('account')->group(function(){
            Route::controller(ControllersAuthController::class)->group(function(){
                Route::get('/', 'me')->name('user.me');
                Route::post('/profile-photo', 'change_profile_photo')->name('user.profilePhoto.change');
                Route::put('/', 'update')->name('user.updateProfile');
                Route::put('/update-password', 'change_password')->name('user.changePassword');
            });

            Route::controller(RelativeController::class)->prefix('relatives')->group(function(){
                Route::post('/', 'store')->name('user.relative.store');
                Route::get('/', 'index')->name('user.relative.index');
                Route::get('/{id}', 'show')->name('user.relative.show');
                Route::post('/{id}', 'update')->name('user.relative.update');
                Route::delete('/{id}', 'destroy')->name('user.relative.delete');
            });

            Route::controller(ControllersMembershipController::class)->prefix('membership')->group(function(){
                Route::get('/types', 'types')->name('user.membershipTypes');
                Route::get('/', 'index')->name('user.membershipInformation');
                Route::put('/', 'update')->name('user.membershipInformation.update');
            });
        });

        Route::prefix('orders')->group(function(){
            Route::controller(ControllersMenuController::class)->prefix('menu')->group(function(){
                Route::get('/{id}', 'index')->name('user.menu.index');
                Route::get('/{id}/modifiers', 'modifiers')->name('user.menu.modifier');
            });
            Route::controller(OrderController::class)->group(function(){
                Route::get('/', 'index')->name('user.order.index');
                Route::get('/past', 'past_orders')->name('user.order.past');
                Route::post('/', 'store')->name('user.order.store');
            });
        });

        Route::prefix('bookings')->controller(BookingController::class)->group(function(){
            Route::get('/', 'index')->name('user.booking.index');
            Route::get('/past', 'pastBookings')->name('user.booking.past');
            Route::get('/{uuid}', 'show')->name('user.booking.show');
            Route::post('/', 'store')->name('user.booking.store');
            Route::post('/{uuid}', 'update')->name('user.booking.update');
            Route::delete('/{uuid}', 'destroy')->name('user.booking,delete');
        });

        Route::prefix('events')->controller(ControllersEventController::class)->group(function(){
            Route::get('/', 'index')->name('user.event.index');
            Route::get('/past', 'pastEvents')->name('user.event.past');
        });

        Route::prefix('payments')->controller(PaymentController::class)->group(function(){
            Route::get('/', 'index')->name('user.payments.index');
            Route::get('/{uuid}', 'show')->name('user.payment.show');
        });

        Route::get('/', [DashboardController::class, 'index'])->name('userDashboard');
    });
});

Route::get('/g5-login', [G5PosService::class, 'login']);
Route::get('/g5-members', [MembershipController::class, 'store_g5_members']);
Route::get('/g5-menu', [MenuController::class, 'store_g5_menu']);
Route::get('/sparkle/login', [SparkleService::class, 'login']);
Route::get('/user-g5-orders/{user_id}', [ControllersAuthController::class, 'get_user_g5_orders']);
Route::get('/store-test-user', [MembershipController::class, 'add_test_user']);