<?php

use App\Http\Controllers\Api\SanctumController;
use App\Http\Controllers\Api\ShowNameController;
use App\Http\Controllers\AssetCategoryController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('/register', [SanctumController::class, 'register']);
Route::get('/login', [SanctumController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum', 'auth.token:RefreshAccessToken']], function () {
    Route::get('/name', [ShowNameController::class, 'showName']);
    Route::apiResource('asset_category', AssetCategoryController::class);
});
Route::POST('/logout', [SanctumController::class, 'logout'])->middleware(['auth:sanctum']);
