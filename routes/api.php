<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
// Public routes for categories
Route::get('/categories', [CategoryController::class, 'index']); // List all categories
Route::get('/categories/{id}', [CategoryController::class, 'show']); // Show a single category
// Protected routes for categories (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']); // Create a new category
    Route::put('/categories/{id}', [CategoryController::class, 'update']); // Update a category
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete a category
});

// Public routes for products
Route::get('/products', [ProductController::class, 'index']); // List all products
Route::get('/products/{id}', [ProductController::class, 'show']); // Show a single product
// Protected routes for products (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']); // Create a new product
    Route::put('/products/{id}', [ProductController::class, 'update']); // Update a product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete a product
});

// Protected routes for orders (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']); // List all orders for authenticated user
    Route::post('/orders', [OrderController::class, 'store']); // Create a new order
    Route::get('/orders/{id}', [OrderController::class, 'show']); // Show a single order
    Route::put('/orders/{id}', [OrderController::class, 'update']); // Update an order
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']); // Delete an order
});
