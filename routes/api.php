<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;


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

        Route::get('category',  'getCategory');
        Route::get('customer',  'getCustomer');
        Route::get('employees',  'getEmployees');

        Route::get('generate_order_id_based_on_format',  'generate_order_id_based_on_format');
        Route::get('products/{category}',  'getProducts');
        Route::get('fractions/{category}',  'getFractions');
        Route::get('rooms',  'getRooms');
        Route::post('room_save',  'saveRoom');

        Route::get('color_partan_model/{product_id}',  'getColorPartanModel');
        Route::get('color_model/{product_id}/{pattern_id}',  'getColorModel');
        Route::get('color_code/{id}',  'getColorCode');
        Route::get('color_code_select/{keyword}/{pattern_id}',  'getColorCodeSelect');
        Route::get('existing_shipping_address/{customer_id}',  'existingShippingAddress');
        Route::get('product_to_attribute/{product_id}',  'getProductToAttribute');

        // Route::get('customer-wise-sidemark/{customer_id}',  'customerWiseSidemark']);
    });

    // Add other routes as needed
    Route::post('customer', [OrderController::class, 'saveCustomer']);
});
