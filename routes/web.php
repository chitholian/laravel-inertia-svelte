<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\IndexController::class, 'home']);

    Route::get('/reports/logins', [\App\Http\Controllers\LoginController::class, 'report']);
    Route::patch('/reports/logins', [\App\Http\Controllers\LoginController::class, 'takeAction']);

    Route::get('/devices', [\App\Http\Controllers\DeviceController::class, 'report']);
    Route::patch('/devices', [\App\Http\Controllers\DeviceController::class, 'takeAction']);

    Route::get('/profile/change-password', [\App\Http\Controllers\LoginController::class, 'changePassForm']);
    Route::patch('/profile/change-password', [\App\Http\Controllers\LoginController::class, 'changePass']);
});

Route::middleware(\App\Http\Middleware\RedirectIfAuthenticated::class)->group(function () {
    Route::get('/login', [\App\Http\Controllers\LoginController::class, 'loginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\LoginController::class, 'doLogin']);
});
Route::any('/logout', [\App\Http\Controllers\LoginController::class, 'doLogout']);
