<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProductController;

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

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

Route::post('register/dropshipper', [AuthController::class, 'registerDropshipper']);

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    // REGISTER
    Route::post('register/warehouse', [AuthController::class, 'registerWarehouseOfficer']);
    Route::post('register/admin', [AuthController::class, 'registerAdmin']);

    // PRODUCT
    Route::post('product', [ProductController::class, 'create']);

    // PROFILE
    Route::get('/profile', function(Request $request) {
        return auth()->user();
    });

    // MARKETPLACE
    Route::get('marketplace', [MarketplaceController::class, 'getAll']);

    // LOGOUT
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logoutall', [AuthController::class, 'logoutAll']);

    // UPLOAD
    Route::post('upload', [CloudinaryController::class, 'upload']);
});
