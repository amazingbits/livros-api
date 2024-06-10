<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BookController;
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

Route::get('/', function () {
    return response()->json([]);
});

// V1
Route::prefix('v1')->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {

        Route::controller(AuthController::class)->group(function () {

            Route::post('/token', 'login')->name('auth.token');
            Route::post('me', 'me')->name('auth.me');
            Route::post('refresh', 'refresh')->name('auth.refresh');
            Route::post('logout', 'logout')->name('auth.logout');
        });
    })->middleware('api');

    Route::prefix('livros')->group(function () {

        Route::controller(BookController::class)->group(function () {

            Route::get('/', 'get')->name('livros.get');

            Route::middleware('auth:api')->group(function () {
                Route::post('/', 'store')->name('livros.store');
                Route::post('/{livroId}/importar-indices-xml', 'importIndexByXML')->name('livros.importIndexByXML');
            });
        });
    });
});
