<?php

use App\Http\Controllers\Dashboard\Admin\ActivityLogController;
use App\Http\Controllers\Dashboard\Admin\AdminController;
use App\Http\Controllers\Dashboard\Admin\Auth\AuthController;
use App\Http\Controllers\Dashboard\Admin\HomeController;
use App\Http\Controllers\Dashboard\Admin\NotificationController;
use App\Http\Controllers\Dashboard\Admin\ProfileController;
use App\Http\Controllers\Dashboard\Admin\RoleController;
use App\Http\Controllers\General\ExcelController;
use App\Http\Controllers\General\FileController;
use App\Http\Controllers\General\NotifyController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\Admin\SettingController as AdminSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\Admin\UserController;
use App\Http\Controllers\Dashboard\Admin\SettingController;
use App\Http\Controllers\Dashboard\Admin\CountryController;
use App\Http\Controllers\Dashboard\Admin\RegionController;
use App\Http\Controllers\Dashboard\Admin\CityController;

use App\Http\Controllers\Dashboard\Admin\ExportController;

use App\Http\Controllers\Dashboard\Admin\FCMNotificationController;
use App\Http\Controllers\Dashboard\Admin\FCMNotificationsController;
    use App\Http\Controllers\Dashboard\Admin\CategoryController;
    use App\Http\Controllers\Dashboard\Admin\BrandController;
    #new_comand_routes_path_here
    
    
    


Broadcast::channel('App.Models.Admin.{id}', function ($admin, $id){
    return (int)$admin->id === (int)$id;
});


Route::post('login', [AuthController::class, 'auth'])->name('login');
Route::get('login', [AuthController::class, 'login'])->middleware('guest:admin');


Route::group(['as' => 'admin.'], function (){

    Route::group(['middleware' => ['auth:admin', 'check.admin.status']], function (){
        broadcast::routes(['middleware' => 'auth:admin']);

        // notifications
        Route::get('notifications', [NotificationController::class, 'getNotifications']);
        Route::get('notifications/mark-as-read/{notification}', [NotificationController::class, 'markAsRead']);
        Route::get('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);


        // `settings`
        Route::get('settings', [SettingController::Class, 'index'])->name('settings.index');
        Route::put('update-settings', [SettingController::Class, 'update'])->name('settings.update');

        // general
        Route::get('export/{export}/{request?}', [ExcelController::class, 'master'])->name('master-export');
        Route::post('send-general-notification/{driver?}', [NotifyController::class, 'notify'])->name('send-general-notification');

        //  activity log
        Route::get('activity-log', [ActivityLogController::Class, 'index'])->name('activity-log');

        // file
        Route::delete('files/{id}', [FileController::class, 'destroy'])->name('files.destroy');
        //logout
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        //home
        Route::get('/', HomeController::class)->name('home');

        // Profile routes
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');


        // `roles`
        Route::resource('roles', RoleController::Class);
        Route::post('roles/multiple', [RoleController::class, 'destroyMultiple'])->name('roles.destroy-multiple');
        Route::post('roles/toggle-status/{role}/{key}', [RoleController::class, 'toggleField'])->name('role-toggle');
        // `admins`
        Route::post('admins/bulk-action', [AdminController::class, 'bulkAction'])->name('admins.bulk-action');
        Route::post('admins/multiple', [AdminController::class, 'destroyMultiple'])->name('admins.destroy-multiple');
        Route::post('admins/toggle-status/{admin}/{key}', [AdminController::class, 'toggleField'])->name('admins-toggle');
        Route::post('admins/export', [AdminController::class, 'export'])->name('admin-export');
        Route::resource('admins', AdminController::Class);

        // `users`
        Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
        Route::post('users/multiple', [UserController::class, 'destroyMultiple'])->name('users.destroy-multiple');
        Route::post('users/toggle-status/{user}/{key}', [UserController::class, 'toggleField'])->name('user-toggle');
        Route::post('users/export', [UserController::class, 'export'])->name('user-export');
        Route::resource('users', UserController::Class);


        // `countries`
        Route::resource('countries', CountryController::Class);
        Route::post('countries/multiple', [CountryController::class, 'destroyMultiple'])->name('countries.destroy-multiple');
        Route::post('countries/toggle-status/{country}/{key}', [CountryController::class, 'toggleField'])->name('country-toggle');

        // `regions`
        Route::resource('regions', RegionController::Class);
        Route::post('regions/multiple', [RegionController::class, 'destroyMultiple'])->name('regions.destroy-multiple');

        // `cities`
        Route::resource('cities', CityController::Class);
        Route::post('cities/multiple', [CityController::class, 'destroyMultiple'])->name('cities.destroy-multiple');

        // exports
        Route::resource('exports', ExportController::class);
        Route::get('exports/{export}/download', [ExportController::class, 'download'])->name('exports.download');


        Route::prefix('fcm-notifications')->name('fcm-notifications.')->group(function () {
            Route::post('send-to-users', [FCMNotificationController::class, 'sendToUsers'])->name('send-to-users');
            Route::post('send-to-all-users', [FCMNotificationController::class, 'sendToAllUsers'])->name('send-to-all-users');
            Route::post('send-by-device-type', [FCMNotificationController::class, 'sendByDeviceType'])->name('send-by-device-type');
            Route::get('stats', [FCMNotificationController::class, 'getStats'])->name('stats');
            Route::get('users-with-devices', [FCMNotificationController::class, 'getUsersWithDevices'])->name('users-with-devices');
        });

        Route::prefix('fcm-notifications')->name('fcm-notifications.')->group(function () {
            Route::get('/', [FCMNotificationsController::class, 'index'])->name('index');
            Route::post('send-to-authenticated', [FCMNotificationsController::class, 'sendToAuthenticatedUsers'])->name('send-to-authenticated');
            Route::post('send-to-guests', [FCMNotificationsController::class, 'sendToGuestUsers'])->name('send-to-guests');
            Route::post('send-to-all-devices', [FCMNotificationsController::class, 'sendToAllDevices'])->name('send-to-all-devices');
            Route::post('send-by-device-type', [FCMNotificationsController::class, 'sendByDeviceType'])->name('send-by-device-type');
            Route::get('stats', [FCMNotificationsController::class, 'getStats'])->name('stats');
        });

    
    // categories
    Route::resource('categories', CategoryController::class);
    Route::post('categories/multiple', [CategoryController::class, 'destroyMultiple'])->name('categories.destroy-multiple');
    Route::post('categories/toggle-status/{category}/{key}', [CategoryController::class, 'toggleField'])->name('category-toggle');
    Route::post('categories/export', [CategoryController::class, 'export'])->name('category-export');
    
    // brands
    Route::resource('brands', BrandController::class);
    Route::post('brands/multiple', [BrandController::class, 'destroyMultiple'])->name('brands.destroy-multiple');
    Route::post('brands/toggle-status/{brand}/{key}', [BrandController::class, 'toggleField'])->name('brand-toggle');
    Route::post('brands/export', [BrandController::class, 'export'])->name('brand-export');
    #new_comand_routes_here



        Route::prefix('egrates')->name('egrates.')->group(function () {
            Route::post('cache-gold', [AdminSettingController::class, 'cacheEgratesGold'])->name('cache-gold');
            Route::post('cache-currencies', [AdminSettingController::class, 'cacheEgratesCurrencies'])->name('cache-currencies');
        });


    });

});
