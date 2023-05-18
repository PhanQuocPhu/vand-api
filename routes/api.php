<?php

use App\Helpers\Routes\RouteHelper;
use App\Http\Controllers\AuthController;
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
Route::prefix('v1')
    ->middleware(['auth:api', 'json.response'])
    ->group(function (){
        RouteHelper::includeRouteFiles(__DIR__ . '/api/v1');
    });
Route::prefix('v1')
    ->middleware(['json.response'])
    ->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(['auth:api']);
        Route::post('/auth/register', [AuthController::class, 'register']);
    });

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
