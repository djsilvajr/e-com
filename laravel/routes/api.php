<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductPriceController;
use App\Http\Controllers\Api\ProductVariantController;

//login
Route::post('/login', [AuthController::class, 'login']);
//Authentication required
Route::middleware('api.stack')->group(function () {
    //Login
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/self', [AuthController::class, 'checkAuth']);
    //User
    Route::post('v1/user', [UserController::class, 'insertUser']);
    Route::get('v1/user/{id}', [UserController::class, 'getUserById']);
    Route::put('v1/user/{id}', [UserController::class, 'putUserById']);
    Route::delete('v1/user/{id}', [UserController::class, 'deleteUserById'])->middleware('api.permission:user.delete');
    Route::patch('v1/user/{id}', [UserController::class, 'patchUserById']);
    //Cache
    Route::post('/cache', [CacheController::class, 'store']);
    Route::get('/cache/{key}', [CacheController::class, 'show']);
    Route::delete('/cache/{key}', [CacheController::class, 'destroy']);
    //Product Type
    Route::get('v1/product/types', [ProductTypeController::class, 'getProductTypes']);
    Route::get('v1/product/type/{id}', [ProductTypeController::class, 'getProductTypeById']);
    Route::get('v1/product/type/{id}/child', [ProductTypeController::class, 'getChildProductTypesById']);
    Route::post('v1/product/type/{id}/child', [ProductTypeController::class, 'createChildProductType']);
    Route::patch('v1/product/type/{id}/status', [ProductTypeController::class, 'changeProductTypeActivationStatus']);
    Route::delete('v1/product/type/{id}/', [ProductTypeController::class, 'deleteProductTypeById']);

    //Product
    Route::post('v1/product', [ProductController::class, 'createProduct']);
    Route::get('v1/product', [ProductController::class, 'getProduct']);
    Route::put('v1/product/{id}', [ProductController::class, 'updateProduct']);
    Route::get('v1/product/{id}', [ProductController::class, 'getProductById']);
    Route::delete('v1/product/{id}', [ProductController::class, 'deleteProduct']);

    //Product Price
    Route::get('v1/product/{product_id}/price', [ProductPriceController::class, 'getPrice']);
    Route::post('v1/product/{product_id}/price', [ProductPriceController::class, 'addPrice']);
    Route::put('v1/product/{product_id}/price', [ProductPriceController::class, 'updatePrice']);
    Route::delete('v1/product/{product_id}/price', [ProductPriceController::class, 'deletePrice']);

    //Product Variant
    Route::get('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'getVariant']);
    Route::post('v1/product/{product_id}/variant', [ProductVariantController::class, 'addVariant']);
    Route::put('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'updateVariant']);
    Route::delete('v1/product/{product_id}/variant/{id}', [ProductVariantController::class, 'deleteVariant']);
});


?>
