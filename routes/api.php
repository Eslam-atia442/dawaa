<?php

use App\Http\Controllers\Api\V1\CountryController;
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
use App\Http\Controllers\Api\V1\GoldpricezController;
use App\Http\Controllers\Api\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\ChangePasswordController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\IntroController;
use App\Http\Controllers\Api\V1\UserController;
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
    Route::post('social-register', SocialRegisterController::class);
    Route::post('forgot-password', ForgotPasswordController::class);
    Route::post('reset-password', ResetPasswordController::class);
    Route::post('resend-code', ResendCodeController::class);
    Route::post('check-otp', CheckOtpController::class);
    Route::post('verify', VerifyController::class);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', LogoutController::class);
    Route::post('refresh-token', RefreshTokenController::class);
    // Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/change-password', [ChangePasswordController::class, 'update']);
    Route::post('profile/delete', [ProfileController::class, 'delete']);
});

Route::get('home', [HomeController::class, 'index']);


Route::get('countries', [CountryController::class, 'index']);
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
