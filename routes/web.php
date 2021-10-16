<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Arete\Sofrosine\Services\Locale;
use App\Http\Controllers\{
    ArticleController,
    AuthController,
    UserController,
    LocaleController,
    SourceController
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

// Redirigir desde el path oficial del RouteServiceProvider::HOME
Route::redirect('home', app('locale')->getLocale() . '/home');

Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => '[a-z]{2}'],
    'middleware' => ['setLocale']
    // 'middleware' => ['setDefaultLocaleURL', 'setLocale']
], function () {

    Route::get('test-locale', function (Request $request, Locale $locale) {
        return $locale->getBestAvailableLocaleFromHTTP() ?? 'naranja';
    });

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
        Route::post('login', [AuthController::class, 'login']) /** @todo add throttle limit */
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


        Route::middleware('auth:sanctum')->group(function () {
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

    Route::group([
        'prefix' => '/articles',
        'as' => 'articles.'
    ], function () {

        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::get('/edit', [ArticleController::class, 'edit'])->name('edit');

        Route::get('/by/{user}', [ArticleController::class, 'indexBy'])
            ->name('by');
        Route::get('/mine', [ArticleController::class, 'mine'])
            ->middleware('auth:sanctum')
            ->name('mine');
        Route::get('/{articleID}', [ArticleController::class, 'show'])
            ->name('show');
    });

    Route::group([
        'prefix' => '/sources',
        'as' => 'sources.'
    ], function () {
        Route::get('/', [SourceController::class, 'index'])->name('index');
        Route::get('/{source}/edit', [SourceController::class, 'edit'])->name('edit');
    });
});

Route::put('/locale', [LocaleController::class, 'update'])->name('locale');

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'prefix' => 'test/prototypes'
], function () {
    Route::view(
        'sharedscope',
        'prototypes.shared-scope',
        [
            'attributes' => ['Server Attribute 1', 'Server Attribute 2']
        ]
    );
    Route::view(
        'nesteddata',
        'prototypes.nested-data'
    );
    Route::view(
        'sharedoptions',
        'prototypes.shared-options'
    );
    Route::view(
        'dinamicsharedoptions',
        'prototypes.dinamic-shared-options'
    );
    Route::view(
        'dinamicfields',
        'prototypes.dinamic-fields'
    );
    Route::view(
        'accordions',
        'prototypes.accordions'
    );

    Route::view('dinamic-store', 'prototypes.dinamic-store');
});

Route::view('livewire', 'livewire');
