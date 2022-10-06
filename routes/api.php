<?php

use App\Http\Controllers\Api\CookiesController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\SanctumController;
use App\Http\Controllers\Api\ShowNameController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetManufacturerController;
use App\Http\Controllers\AssetModelController;
use App\Http\Controllers\Api\ThemesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('/login', [SanctumController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum', 'auth.token:RefreshAccessToken']], function(){
    Route::GET('/name', [ShowNameController::class, 'showName']);
    Route::PATCH('/changepassword', [SanctumController::class, 'changePassword']);
    Route::PATCH('/themes', [CookiesController::class, 'patchThemes']);
    Route::PATCH('/languages', [CookiesController::class, 'patchLanguages']);
    Route::apiResource('asset_category', AssetCategoryController::class);
    Route::apiResource('asset_manufacturer', AssetManufacturerController::class);
    Route::apiResource('asset_model', AssetModelController::class);
});
Route::POST('/forgotpassword', [SanctumController::class, 'forgotPassword']);
Route::PATCH('/resetpassword', [SanctumController::class, 'resetPassword']);
Route::POST('/logout', [SanctumController::class, 'logout'])->middleware(['auth:sanctum']);
Route::GET('/themes', [ThemesController::class, 'get']);
Route::GET('/languages', [LanguagesController::class, 'get']);