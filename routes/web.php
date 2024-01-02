<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
// use App\Http\Contr

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {

    // Redis::hmset('student', [
    //     'title' => 'title',
    //     'description' => 'description',
    // ]);

    // return  Redis::hgetall('student');
    // return Redis::get('name');
    // return view('welcome');
});


// Route::resource('order', PhotoController::class);

