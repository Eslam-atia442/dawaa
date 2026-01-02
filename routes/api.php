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

Route::group(['middleware' => 'guest:sanctum'], function () {
    Route::post('login', LoginController::class);
    Route::post('register', RegisterController::class);
    Route::post('social-login', SocialLoginController::class);
    Route::post('social-register', SocialRegisterController::class);
    Route::post('forgot-password', ForgotPasswordController::class);
    Route::post('reset-password', ResetPasswordController::class);
    Route::post('resend-code', ResendCodeController::class);
    Route::post('check-otp', CheckOtpController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', LogoutController::class);
    Route::post('refresh-token', RefreshTokenController::class);
    Route::post('verify', VerifyController::class);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/change-password', [ChangePasswordController::class, 'update']);
    Route::post('profile/delete', [ProfileController::class, 'delete']);

});

Route::get('home', [HomeController::class, 'index']);


Route::get('countries', [CountryController::class, 'index']);
Route::get('settings', [SettingController::class, 'index']);

Route::prefix('egrates')->group(function () {
    Route::get('usd-prices', [App\Http\Controllers\Api\V1\EgratesController::class, 'getUsdPrices']);
    Route::get('eur-prices', [App\Http\Controllers\Api\V1\EgratesController::class, 'getEurPrices']);
    Route::get('gbp-prices', [App\Http\Controllers\Api\V1\EgratesController::class, 'getGbpPrices']);
    Route::get('aed-prices', [App\Http\Controllers\Api\V1\EgratesController::class, 'getAedPrices']);
    Route::get('gold-prices', [App\Http\Controllers\Api\V1\EgratesController::class, 'getGoldPrices']);
    Route::get('banks', [App\Http\Controllers\Api\V1\EgratesController::class, 'getBanks']);
});

Route::prefix('goldpricez')->group(function () {
    Route::get('gold', [GoldpricezController::class, 'getGoldRates']);
    Route::get('silver', [GoldpricezController::class, 'getSilverRates']);
});

Route::group(['prefix' => 'devices'], function () {
    Route::post('/fcm-token', [DeviceController::class, 'manageFCMToken']);
});
