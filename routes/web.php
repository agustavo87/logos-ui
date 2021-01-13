<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    AuthController,
    UserController,
    LocaleController
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

Route::redirect('home', config('locale.languages.default') . '/home');

Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => '[a-z]{2}'],
    'middleware' => ['setDefaultLocaleURL', 'setLocale']
], function () {
    
    Route::get('/', function () {
        return view('landing');
    })->name('landing');
    
    Route::get('home', function (Request $request) {
        return view('home');
    })->name('home');

    Route::name('auth.')->group(function () {
        Route::get('login', [AuthController::class, 'show'])
            ->name('show')
            ->middleware('guest');
        Route::post('login', [AuthController::class, 'login']) // add throttle limit
            ->name('login')
            ->middleware('guest');
        Route::get('logout', [AuthController::class, 'logout'])
            ->name('logout')
            ->middleware('auth');
    });
    
    Route::group([
        'prefix' => 'users',
        'as' => 'users.'
    ], function () {

        Route::get('/create', [UserController::class, 'create'])
            ->name('create')
            ->middleware('guest');

        Route::post('', [UserController::class, 'store'])
            ->name('register'); // limit somehow

            
        Route::middleware('auth')->group(function () {
            Route::get('', [UserController::class, 'index'])
                ->name('index')
                ->middleware('can:viewAny,App\Model\User');
                
            Route::get('/{user}', [UserController::class, 'show'])
                ->name('show')
                ->middleware('can:view,user');

            Route::get('/{user}/edit', [UserController::class, 'edit'])
                ->name('edit')
                ->middleware('can:update,user');
            
            Route::put('/{user}', [UserController::class, 'update'])
                ->name('update');
            
            Route::delete('/{user}', [UserController::class, 'destroy'])
                ->name('delete');
            
        });
    });
});

Route::put('/locale', [LocaleController::class, 'update'])->name('locale');



