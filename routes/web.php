<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    AuthController,
    UserController
};

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

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('home', function (Request $request) {
    return view('home');
})->name('home');


Route::name('user.')->group(function () {
    Route::get('user/index', [UserController::class, 'index'])
        ->name('index');

    Route::get('user/create', [UserController::class, 'create'])
        ->name('create');

    Route::post('user', [UserController::class, 'store'])
        ->name('register');

    Route::get('user/{user}', [UserController::class, 'show'])
        ->name('show');

    Route::get('user/{user}/edit', [UserController::class, 'edit'])
        ->name('edit');

    Route::put('user/{user}', [UserController::class, 'update'])
        ->name('update');

    Route::delete('user/{user}', [UserController::class, 'destroy'])
        ->name('delete');
});

Route::name('auth.')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('identify', [AuthController::class, 'identify'])->name('identify');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});
