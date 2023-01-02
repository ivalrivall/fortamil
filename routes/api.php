<?php

use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
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

// V1 API
Route::prefix('v1')->group(function() {
    // AUTH
    Route::post('register/dropshipper', [AuthController::class, 'registerDropshipper']);
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum', 'ability:basic']], function () {
        // ADDRESS
        Route::get('address/{userId}', [AddressController::class, 'getAddressByUserId'])->middleware(['ability:super_admin']);

        // CART
        Route::post('cart/product', [CartController::class, 'addProduct'])->middleware(['ability:cart,cart.add_product']);
        Route::delete('cart/product', [CartController::class, 'removeProduct'])->middleware(['ability:cart,cart.remove']);
        Route::post('cart/quantity', [CartController::class, 'editQuantity'])->middleware(['ability:cart,cart.edit_quantity']);
        Route::get('cart/empty', [CartController::class, 'emptyCart'])->middleware(['ability:cart,cart.empty_quantity']);;
        Route::get('user/carts', [UserController::class, 'getCarts'])->middleware(['ability:cart']);

        // CATEGORY
        Route::get('category/get-all', [CategoryController::class, 'getAllCategory']);

        // CUSTOMER
        Route::post('customer-with-address', [CustomerController::class, 'createWithAddress'])->middleware([
            'ability:customer.create,dropshipper'
        ]);

        // DASHBOARD
        Route::get('dashboard/statistic', [DashboardController::class, 'getStatisticData'])->middleware(['ability:super_admin']);

        // LOGOUT
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logoutall', [AuthController::class, 'logoutAll']);

        // MARKETPLACE
        Route::get('marketplace', [MarketplaceController::class, 'getAll']);

        // NOTIFICATION
        Route::get('notif/paginate', [NotificationController::class, 'paginateUserNotif']);
        Route::get('notif/read-all', [NotificationController::class, 'readAll']);
        Route::get('notif/{notifId}/read', [NotificationController::class, 'markRead']);
        Route::get('notif/{notifId}/unread', [NotificationController::class, 'markUnread']);

        // ORDER
        Route::post('order', [OrderController::class, 'create'])->middleware(['ability:dropshipper,admin,super_admin']);
        Route::get('order/paginate', [OrderController::class, 'getUserOrder']);
        Route::get('order/{orderId}', [OrderController::class, 'getDetailOrder'])->middleware(['ability:dropshipper,admin,super_admin,warehouse_officer']);
        Route::post('order/{orderId}/reject', [OrderController::class, 'rejectOrder'])->middleware(['ability:admin']);
        Route::get('order/{orderId}/accept', [OrderController::class, 'acceptOrder'])->middleware(['ability:admin']);
        Route::post('order/scan', [OrderController::class, 'scanOrderProduct'])->middleware(['ability:warehouse_officer']);
        Route::post('order/upload-proof-packing', [OrderController::class, 'uploadProofPacking'])->middleware(['ability:warehouse_officer']);
        Route::post('order/confirm-arrived', [OrderController::class, 'confirmArrived'])->middleware(['ability:dropshipper']);

        // PAYMENT METHOD
        Route::get('payment-method', [PaymentMethodController::class, 'getAll']);

        // PAYMENT
        Route::post('payment/pay-order',[PaymentController::class, 'payOrder']);

        // PRODUCT
        Route::post('product', [ProductController::class, 'create'])->middleware(['ability:product.create']);
        Route::post('product/{productId}/stock/reduce', [ProductController::class, 'reduceStock'])->middleware(['ability:product.stock.reduce']);
        Route::get('product/warehouse', [ProductController::class, 'getProductByWarehouse'])->middleware(['ability:dropshipper,admin']);
        Route::post('product/{productId}/disable', [ProductController::class, 'disableProduct'])->middleware(['ability:super_admin']);

        // PROFILE
        Route::get('profile', [AuthController::class, 'getProfile']);

        // REGISTER
        Route::post('register/warehouse', [AuthController::class, 'registerWarehouseOfficer'])->middleware(['ability:admin,super_admin']);
        Route::post('register/admin', [AuthController::class, 'registerAdmin'])->middleware(['ability:super_admin']);
        Route::post('register/sa', [AuthController::class, 'registerSuperAdmin'])->middleware(['ability:super_admin']);
        Route::post('register/cashier', [AuthController::class, 'registerCashier'])->middleware(['ability:admin,super_admin']);

        // RETURN
        Route::post('return/request', [ReturnController::class, 'requestReturn'])->middleware(['ability:dropshipper']);

        // ROLE
        Route::get('roles', [RoleController::class, 'getRoles'])->middleware(['ability:super_admin']);

        // STORE
        Route::post('store', [StoreController::class, 'create'])->middleware(['ability:store.create']);
        Route::get('store/paginate', [StoreController::class, 'paginate'])->middleware(['ability:store.read']);
        Route::post('store/update', [StoreController::class, 'update'])->middleware(['ability:store.update']);
        Route::get('store/{id}', [StoreController::class, 'getById'])->middleware(['ability:store.read']);
        Route::delete('store/{id}', [StoreController::class, 'deleteById'])->middleware(['ability:store.delete']);

        // TEST
        Route::resource('test', TestController::class);

        // UPLOAD
        Route::post('upload', [CloudinaryController::class, 'upload']);

        // USER
        Route::get('user/paginate', [UserController::class, 'getPaginate'])->middleware(['ability:super_admin,admin']);
        Route::post('user/{userId}/disable', [UserController::class, 'disableUser'])->middleware(['ability:super_admin']);
        Route::delete('user/{userId}', [UserController::class, 'forceDelete'])->middleware(['ability:super_admin']);
        Route::post('user', [UserController::class, 'createUser'])->middleware(['ability:super_admin']);
        Route::get('user/{userId}', [UserController::class, 'getUserById'])->middleware(['ability:super_admin']);
        Route::post('user/edit', [UserController::class, 'editUser'])->middleware(['ability:super_admin']);

        // WAREHOUSE
        Route::get('warehouse/paginate', [WarehouseController::class, 'paginate']);
        Route::post('warehouse', [WarehouseController::class, 'create']);
        Route::get('warehouse/search', [WarehouseController::class, 'searchWarehouse']);
        Route::get('warehouse/{warehouseId}/product', [WarehouseController::class, 'getProductByWarehousePaginate']);
        Route::post('warehouse/{warehouseId}/edit', [WarehouseController::class, 'editWarehouse'])->middleware(['ability:super_admin,admin']);
    });

    // INDONESIA REGION
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

    // MOBILE
    Route::get('mobile/version', function() {
        return env('MOBILE_APP_VERSION', '0.0.1');
    });

});
