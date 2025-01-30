<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin;

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

Route::get('/', function () {
    return view('welcome');
});


require __DIR__.'/auth.php';

/* Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
}); 

Route::middleware(['auth:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::resource('users', UserController::class); // resource ルート定義
}); 
*/

Route::middleware(['auth:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home'); // admin.home ルートを定義
    Route::resource('users', UserController::class);
});