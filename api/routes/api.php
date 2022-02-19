<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Whoops\Run;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('company')
    ->controller(CompanyController::class)
    ->group(function(){
        Route::get('/', 'all');
        Route::get('/{id}', 'getById');
        Route::post('/', 'store');
        Route::put('/{id}', 'edit');
        Route::delete('/{id}', 'destroy');
    });

    Route::prefix('supplier')
    ->controller(SupplierController::class)
    ->middleware('ageVerify')
    ->group(function(){
        Route::get('/', 'all');
        Route::get('/{id}', 'getById');
        Route::post('/', 'store');
        Route::put('/{id}', 'edit');
        Route::delete('/{id}', 'destroy');
    });
