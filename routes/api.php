<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WarehouseController;

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
    Route::group(['middleware' => ['role.all']], function () {
        // PRODUCT
        Route::post('product', [ProductController::class, 'create']);

        // MARKETPLACE
        Route::get('marketplace', [MarketplaceController::class, 'getAll']);

        // LOGOUT
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logoutall', [AuthController::class, 'logoutAll']);

        // UPLOAD
        Route::post('upload', [CloudinaryController::class, 'upload']);

        // PROFILE
        Route::get('/profile', function(Request $request) {
            return auth()->user();
        });

        // CUSTOMER
        Route::post('customer-with-address', [CustomerController::class, 'createWithAddress']);

        // WAREHOUSE
        Route::get('warehouse/paginate', [WarehouseController::class, 'paginate']);
        Route::post('warehouse', [WarehouseController::class, 'create']);

        Route::group(['middleware' => ['dropshipper']], function () {
            // STORE
            Route::post('store', [StoreController::class, 'create']);
            Route::get('store/paginate', [StoreController::class, 'paginate']);

            // CART
            Route::post('cart/product', [CartController::class, 'addProduct']);
            Route::delete('card/product/{id}', [CartController::class, 'removeProduct']);

            // ORDER
            Route::post('order', [OrderController::class, 'create']);
        });

        Route::group(['middleware' => ['admin']], function () {
            // REGISTER
            Route::post('register/warehouse', [AuthController::class, 'registerWarehouseOfficer']);
            Route::post('register/admin', [AuthController::class, 'registerAdmin']);
        });
    });
});

Route::get('get-provinces', [RegionController::class, 'getProvinces']);
Route::get('province/{provinceId}', [RegionController::class, 'getProvince']);
Route::get('get-cities/{provinceId}', [RegionController::class, 'getCities']);
Route::get('city/{cityId}', [RegionController::class, 'getCity']);
Route::get('get-all-cities', [RegionController::class, 'getAllCities']);
Route::get('get-districts/{cityId}', [RegionController::class, 'getDistricts']);
Route::get('get-all-districts', [RegionController::class, 'getAllDistricts']);
Route::get('district/{districtId}', [RegionController::class, 'getDistrict']);
Route::get('get-villages/{districtId}', [RegionController::class, 'getVillages']);
Route::get('village/{villageId}', [RegionController::class, 'getVillage']);
Route::post('get-regions', [RegionController::class, 'getRegions']);
Route::post('get-regions-paginate', [RegionController::class, 'getPaginateRegions']);
