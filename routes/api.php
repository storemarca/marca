<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RegionsController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/slug/{slug}', [ProductController::class, 'showBySlug']);
Route::get('/products/search/{query}', [ProductController::class, 'search']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/slug/{slug}', [CategoryController::class, 'showBySlug']);
Route::get('/categories/{id}/products', [CategoryController::class, 'products']);

// Regions
Route::prefix('regions')->group(function () {
    Route::get('/countries', [RegionsController::class, 'getCountries']);
    Route::get('/countries/{id}', [RegionsController::class, 'getCountry']);
    Route::get('/countries/{countryId}/governorates', [RegionsController::class, 'getGovernorates']);
    Route::get('/governorates/{id}', [RegionsController::class, 'getGovernorate']);
    Route::get('/governorates/{governorateId}/districts', [RegionsController::class, 'getDistricts']);
    Route::get('/districts/{id}', [RegionsController::class, 'getDistrict']);
    Route::get('/districts/{districtId}/areas', [RegionsController::class, 'getAreas']);
    Route::get('/areas/{id}', [RegionsController::class, 'getArea']);
    Route::post('/shipping-cost', [RegionsController::class, 'calculateShippingCost']);
    Route::get('/hierarchy', [RegionsController::class, 'getRegionsHierarchy']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    
    // Addresses
    Route::get('/addresses', [UserController::class, 'addresses']);
    Route::post('/addresses', [UserController::class, 'storeAddress']);
    Route::put('/addresses/{address}', [UserController::class, 'updateAddress']);
    Route::delete('/addresses/{address}', [UserController::class, 'destroyAddress']);
    Route::put('/addresses/{address}/default', [UserController::class, 'setDefaultAddress']);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::delete('/cart', [CartController::class, 'clear']);
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);
    Route::delete('/cart/remove-coupon', [CartController::class, 'removeCoupon']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
}); 