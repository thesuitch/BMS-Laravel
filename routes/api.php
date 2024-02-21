<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RoomController;
use App\Models\Customer;

// Authentication Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('Unauthenticated', [AuthController::class, 'Unauthenticated'])->name('Unauthenticated');

Route::middleware('auth:api')->group(function () {
    // Authenticated Routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('user', [AuthController::class, 'user']);

    // Order Routes
    Route::controller(OrderController::class)->prefix('order')->group(function () {

        Route::get('getCategoryProducts',  'getCategoryProducts');
        Route::get('attributes/{product_id}',  'getAttributes');
        Route::get('employees',  'getEmployees');
        Route::get('generate_order_id_based_on_format',  'generate_order_id_based_on_format');
        Route::get('existing_shipping_address/{customer_id}',  'existingShippingAddress');
        Route::get('get_product_attr_op_op_op/{opOpId}/{proAttOpOpId}/{attributeId}/{mainPrice}/{selectedOptionTypeOpOp}',  'get_product_attr_op_op_op');
        

        Route::get('calculate/{upConditionHeight}/{upConditionHeightFraction}/{upConditionWidth}/{upConditionWidthFraction}/{upAttributeId}/{upLevel}/{productId}/{patternId}',  'calculateUpCondition');
        Route::get('price/{height}/{width}/{product_id}/{pattern_id}/{product_type}',  'getProductRowColPrice');

        Route::post('store', 'store');


    });

    // Customer
    Route::controller(CustomerController::class)->prefix('customer')->group(function () {
        Route::post('store', 'store');
        Route::get('dropdown',  'getCustomer');
    });

    // Room
    Route::controller(RoomController::class)->prefix('room')->group(function () {
        Route::post('store', 'store');
        Route::get('dropdown', 'getRooms');
    });
});
