<?php

use App\Http\Controllers\Api\CookiesController;
use App\Http\Controllers\AvatarController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});
Route::get('/resetpassword', function() {
    return "For now there isn't anything there. <br>
    For actual password reset use /api/resetpassword endpoint";
});
Route::get('nulltest', [CookiesController::class, 'nullTest']);
