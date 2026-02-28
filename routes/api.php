<?php

use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\RegionController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\SocialLoginController;
use App\Http\Controllers\Api\V1\Auth\SocialRegisterController;
use App\Http\Controllers\Api\V1\Auth\SettingController;
use App\Http\Controllers\Api\V1\Auth\VerifyController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\Auth\ResendCodeController;
use App\Http\Controllers\Api\V1\Auth\CheckOtpController;
use App\Http\Controllers\Api\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\ChangePasswordController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\IntroController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\StoreController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ChildProductController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\RefundController;
use App\Http\Controllers\Api\V1\SliderController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ContactUsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'devices'], function () {
    Route::post('/fcm-token', [DeviceController::class, 'manageFCMToken']);
});
Route::group(['middleware' => 'guest:sanctum'], function () {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
    Route::post('social-login', SocialLoginController::class);
    // Route::post('social-register', SocialRegisterController::class);
    Route::post('forgot-password', ForgotPasswordController::class);
    // Route::post('reset-password', ResetPasswordController::class);
    Route::post('resend-code', ResendCodeController::class);
    Route::post('check-otp', CheckOtpController::class);
    Route::post('verify', VerifyController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', LogoutController::class);
    Route::post('refresh-token', RefreshTokenController::class);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/change-password', [ChangePasswordController::class, 'update']);
    Route::post('profile/delete', [ProfileController::class, 'delete']);

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    Route::group(['prefix' => 'wallet'], function () {
        Route::get('/', [WalletController::class, 'getWallet']);
        Route::get('/history', [WalletController::class, 'getHistory']);
    });
});


Route::get('countries', [CountryController::class, 'index']);

Route::group(['prefix' => 'regions'], function () {
    Route::get('/', [RegionController::class, 'index']);
    Route::get('/country/{country}', [RegionController::class, 'getByCountry']);
});

Route::group(['prefix' => 'cities'], function () {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/region/{region}', [CityController::class, 'getByRegion']);
});

Route::get('settings', [SettingController::class, 'index']);

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

Route::group(['prefix' => 'brands'], function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::get('/{id}', [BrandController::class, 'show']);
});

Route::group(['prefix' => 'intros'], function () {
    Route::get('/', [IntroController::class, 'index']);
    Route::get('/{id}', [IntroController::class, 'show']);
});

Route::group(['prefix' => 'users'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
});

Route::group(['prefix' => 'stores'], function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::get('/{id}', [StoreController::class, 'show']);
});

Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/{product}/child-products', [ChildProductController::class, 'getByProduct']);
});

Route::group(['prefix' => 'child-products'], function () {
    Route::get('/', [ChildProductController::class, 'index']);
    Route::get('/{id}', [ChildProductController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'accepted.user'])->group(function () {
    Route::group(['prefix' => 'cart'], function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/remove', [CartController::class, 'removeFromCart']);
        Route::delete('/empty', [CartController::class, 'emptyCart']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'createOrder']);
    });

    Route::group(['prefix' => 'refunds'], function () {
        Route::get('/refundable-orders', [RefundController::class, 'getRefundableOrders']);
        Route::get('/orders/{order}/refundable-items', [RefundController::class, 'getRefundableItems']);
        Route::post('/', [RefundController::class, 'createRefund']);
    });
});

Route::group(['prefix' => 'sliders'], function () {
    Route::get('/', [SliderController::class, 'index']);
    Route::get('/{id}', [SliderController::class, 'show']);
});

Route::group(['prefix' => 'contactuses'], function () {
    // Route::get('/', [ContactUsController::class, 'index']);
    // Route::get('/{id}', [ContactUsController::class, 'show']);
    Route::post('/', [ContactUsController::class, 'store']);
});
