<?php

use App\Http\Controllers\DishController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Additional_ingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Auth::routes();
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/role/{id}', [RoleController::class, 'show']);
Route::post('/role/create', [RoleController::class, 'store']);
Route::put('/role/update/{id}', [RoleController::class, 'update']);

Route::post('/user/create', [UserController::class, 'store']);
Route::get('/user/{id}', [UserController::class, 'show']);

Route::get('/dish/{id}', [DishController::class, 'show']);
Route::post('/dish/create', [DishController::class, 'store']);
Route::post('/dish/update/{id}', [DishController::class, 'update']);
Route::delete('/dish/delete/{id}', [DishController::class, 'destroy']);

Route::get('/ingredient', [IngredientController::class, 'index']);
Route::get('/ingredient/{id}', [IngredientController::class, 'show']);
Route::post('/ingredient/create', [IngredientController::class, 'store']);
Route::post('/ingredient/update/{id}', [IngredientController::class, 'update']);
Route::delete('/ingredient/delete/{id}', [IngredientController::class, 'destroy']);

Route::get('/location/{id}', [LocationController::class, 'show']);
Route::post('/location/create', [LocationController::class, 'store']);
Route::post('/location/update/{id}', [LocationController::class, 'update']);
Route::delete('/location/delete/{id}', [LocationController::class, 'destroy']);

Route::get('/additional_ing', [Additional_ingController::class, 'index']);
Route::get('/additional_ing/{id}', [Additional_ingController::class, 'show']);
Route::post('/additional_ing/update/{id}', [Additional_ingController::class, 'update']);
Route::delete('/additional_ing/delete/{id}', [Additional_ingController::class, 'destroy']); 