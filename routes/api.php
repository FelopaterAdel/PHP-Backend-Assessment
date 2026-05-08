<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::post('/products/{id}/stock', [ProductController::class, 'adjustStock']);
