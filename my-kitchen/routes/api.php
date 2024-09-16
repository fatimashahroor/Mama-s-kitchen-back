<?php

use App\Http\Controllers\DishController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Additional_ingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
});
Route::post('/refresh-token', 'AuthController@refreshToken');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/role', [RoleController::class, 'index']);
Route::get('/role/{id}', [RoleController::class, 'show']);
Route::post('/role/create', [RoleController::class, 'store']);
Route::put('/role/update/{id}', [RoleController::class, 'update']);
Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);

Route::get('/user', [UserController::class, 'index']);
Route::post('/user/create', [UserController::class, 'store']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::post('/user/update/{id}', [UserController::class, 'update']);
Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);
Route::get('/cooks', [UserController::class, 'listCooks']);
Route::post('/user/rating', [UserController::class, 'setRating']);
Route::get('/user/rating/{user_id}', [UserController::class, 'getOverallRating']);

Route::get('/dish', [DishController::class, 'index']);
Route::get('/dish/{id}', [DishController::class, 'show']);
Route::post('/dish/create', [DishController::class, 'store']);
Route::post('/dish/update/{id}', [DishController::class, 'update']);
Route::delete('/dish/delete/{id}', [DishController::class, 'destroy']);
Route::get('/dishes/{user_id}', [DishController::class, 'getDishesByUser']);
Route::get('/dish/ingredients/{id}', [DishController::class, 'getDishIngredients']);
Route::get('/image/{filename}', function ($filename) {
    $path = storage_path('public/' . $filename);
    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});
Route::get('/location', [LocationController::class, 'index']);
Route::get('/location/{id}', [LocationController::class, 'show']);
Route::post('/location/create', [LocationController::class, 'store']);
Route::post('/location/update/{id}', [LocationController::class, 'update']);
Route::delete('/location/delete/{id}', [LocationController::class, 'destroy']);

Route::get('/additional_ing', [Additional_ingController::class, 'index']);
Route::post('/additional_ing/create/{user_id}', [Additional_ingController::class, 'store']);
Route::get('/additional_ing/{id}', [Additional_ingController::class, 'show']);
Route::post('/additional_ing/update/{id}', [Additional_ingController::class, 'update']);
Route::delete('/additional_ing/delete/{id}', [Additional_ingController::class, 'destroy']); 

Route::get('/order', [OrderController::class, 'getCurrentUserDishes']);
Route::post('/order/create', [OrderController::class, 'store']);
Route::get('/order/{id}', [OrderController::class, 'show']);
Route::post('/order/update/{id}', [OrderController::class, 'update']);

Route::get('/payment', [PaymentController::class, 'index']);
Route::post('/payment/create', [PaymentController::class, 'store']);
Route::get('/payment/{id}', [PaymentController::class, 'show']);

Route::get('/review', [ReviewController::class, 'index']);
Route::post('/review/create', [ReviewController::class, 'store']);
Route::get('/review/{dish_id}', [ReviewController::class, 'getDishReviews']);
Route::post('/review/update/{id}', [ReviewController::class, 'update']);
Route::delete('/review/delete/{id}', [ReviewController::class, 'destroy']);