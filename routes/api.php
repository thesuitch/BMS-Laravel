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

Route::middleware('jwt.verify')->group(function () {
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
        Route::get('edit/{orde_id}', 'editOrder');
        Route::put('update', 'UpdateOrder');
        Route::get('edit/item/{id}', 'editOrderItem');
        Route::put('update/item', 'UpdateOrderItem');
        Route::get('index', 'index');
        Route::get('quotes', 'quotes');
        Route::get('receipt/{order_id}', 'receipt');
        Route::put('stage/update/{stage}/{orderId}', 'setOrderStage');
        Route::get('/retailer-order-stages','getAllRetailerOrderStage');

        Route::post('modify_amount', 'modify_amount');
        Route::get('filter_options', 'filterOptions');

        // Delete Apis
        Route::delete('delete/{order_id}', 'deleteOrder');
        Route::delete('delete_multi_orders', 'deleteMultiOrders');
        Route::delete('order_product_delete/{row_id}', 'order_product_delete');
        Route::delete('order_controller_delete/{row_id}', 'OrderControllerDelete');
        Route::delete('order_component_delete/{row_id}', 'OrderComponentDelete');
        Route::delete('order_hardware_delete/{row_id}', 'OrderHardwareDelete');
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
