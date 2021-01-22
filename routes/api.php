<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('test', function (Request $request) {
    if (!$request->has('action')) {
        return $request->all();
    }
    switch ($request->action) {
        case 'get':
            return [
                'data' => $request->session()->get('data', 'unfound')
            ];
            break;
        case 'set':
            $request->session()->put('data', $request->data);
            return 'set';
            break;
        case 'echo': 
            return $request->all();
        default: 
            return 'unknownn';
    }
});



Route::post('/articles', [ArticleController::class, 'store'])
    ->name('articles.store')
    ->middleware('auth:sanctum');

Route::get('/articles/search', [ArticleController::class, 'search'])
    ->name('articles.search')
    ->middleware('auth:sanctum');