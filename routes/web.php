<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
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


// Route::get('home', function () {
//     return Inertia::render('Welcome', [
//      'msg' => 'Hola Gustavo',
//     ]);
// })->name('home');


// Route::get('login', [AuthController::class, 'show'])
//     ->name('auth.show');

// Route::post('login', [AuthController::class, 'authenticate'])
//     ->name('auth.login');

// Route::get('logout', [AuthController::class, 'logout'])
//     ->name('auth.logout');
