<?php

use App\Http\Controllers\Api\CookiesController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\SanctumController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\AssetModelController;
use App\Http\Controllers\Api\ThemesController;
use App\Http\Controllers\AssetComponentCategoryController;
use App\Http\Controllers\AssetComponentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetFileController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LicencablesController;
use App\Http\Controllers\LicenceCategoryController;
use App\Http\Controllers\LicenceController;
use App\Http\Controllers\LicenceFileController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StatisticsController;
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
Route::patch('/activateaccount', [UserController::class, 'activateAccount']);

Route::group(['middleware' => ['auth:sanctum', 'auth.token:RefreshAccessToken']], function () {
    Route::PATCH('/changepassword', [SanctumController::class, 'changePassword']);
    Route::PATCH('/themes', [CookiesController::class, 'patchThemes']);
    Route::PATCH('/languages', [CookiesController::class, 'patchLanguages']);
    Route::apiResource('asset_category', AssetCategoryController::class);
    Route::apiResource('asset_model', AssetModelController::class);
    Route::get('/user/permissions', [UserController::class, 'showPermissions']);
    Route::patch('/user/massassign/{id}', [UserController::class, 'massAssignRoles']);
    Route::patch('/user/removerole/{id}', [UserController::class, 'removeRole']);
    Route::post('/user/edit', [UserController::class, 'setUserDetails']);
    Route::get('/user/self', [UserController::class, 'show']);
    Route::apiResource('/user', UserController::class);
    Route::apiResource('/permission', PermissionController::class);
    Route::get('/role/users/{id}', [RoleController::class, 'rolesWithUsers']);
    Route::apiResource('/role', RoleController::class);
    Route::get('/avatar', [UserController::class, 'showAvatar']);
    Route::get('/avatar/{id}', [UserController::class, 'showAvatar']);

    Route::apiResource('/licence_category', LicenceCategoryController::class);
    Route::prefix('licence')->group(function () {
        Route::apiResource('/', LicenceController::class);
        Route::get('/{id}', [LicenceController::class, 'show']);
        Route::patch('/{id}', [LicenceController::class, 'update']);
        Route::delete('/{id}', [LicenceController::class, 'destroy']);
        Route::get('/{id}/history', [LicenceController::class, 'showHistory']);
        Route::get('/{licenceId}/file/{id}/download', [LicenceFileController::class, 'download']);
        Route::apiResource('/{licenceId}/licencables', LicencablesController::class);
        Route::apiResource('/{licenceId}/file', LicenceFileController::class);
    });

    Route::get('licencables', [LicencablesController::class, 'indexAll']);

    Route::apiResource('manufacturer', ManufacturerController::class);

    Route::delete('asset/{asset}/image', [AssetController::class, 'destroyImage']);
    Route::post('asset/{asset}/image', [AssetController::class, 'storeImage']);
    Route::get('asset/{asset}/image{extension?}', [AssetController::class, 'downloadImage']);

    Route::get('/asset/{asset}/qr', [AssetController::class, 'qr_code']);
    Route::apiResource('asset', AssetController::class);

    Route::apiResource('asset_file', AssetFileController::class);
    Route::get('/asset_file/{assetFile}/download', [AssetFileController::class, 'download']);

    Route::get('/statistics', [StatisticsController::class, 'index']);
    Route::get('/search', [SearchController::class, 'search']);

    Route::get('log', [LogController::class, 'index']);

    Route::apiResource('asset_maintenance', AssetMaintenanceController::class);
    Route::apiResource('asset_component_category', AssetComponentCategoryController::class);
    Route::apiResource('asset_component', AssetComponentController::class);

    Route::get('category', [CategoryController::class, 'index']);
});

Route::POST('/forgotpassword', [SanctumController::class, 'forgotPassword']);
Route::PATCH('/resetpassword', [SanctumController::class, 'resetPassword']);
Route::POST('/logout', [SanctumController::class, 'logout'])->middleware(['auth:sanctum']);
Route::GET('/themes', [ThemesController::class, 'get']);
Route::GET('/languages', [LanguagesController::class, 'get']);
