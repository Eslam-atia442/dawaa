<?php

use App\Http\Controllers\Admin\V1\CategoryController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::view('/', 'welcome')->name('home');
Route::view('privacy-policy', 'terms');

// Deep Link Routes - Smart Landing Pages
Route::get('/certificate-details', function (\Illuminate\Http\Request $request) {
    return view('deep-link-landing', [
        'title' => 'Certificate Details - Naseh',
        'description' => 'View certificate details in the Naseh app',
    ]);
})->name('deep-link.certificate-details');

Route::get('/gold-fund-details', function (\Illuminate\Http\Request $request) {
    return view('deep-link-landing', [
        'title' => 'Gold Fund Details - Naseh',
        'description' => 'View gold fund details in the Naseh app',
    ]);
})->name('deep-link.gold-fund-details');

Route::get('/metals', function (\Illuminate\Http\Request $request) {
    return view('deep-link-landing', [
        'title' => 'Metals - Naseh',
        'description' => 'View metals information in the Naseh app',
    ]);
})->name('deep-link.metals');

Route::get('/currency', function (\Illuminate\Http\Request $request) {
    return view('deep-link-landing', [
        'title' => 'Currency - Naseh',
        'description' => 'View currency information in the Naseh app',
    ]);
})->name('deep-link.currency');

