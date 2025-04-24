<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Route untuk mendapatkan semua produk
Route::get('/products', [ProductController::class, 'getAllProducts']);

// Route untuk mendapatkan detail produk berdasarkan ID
Route::get('/products/{productId}', [ProductController::class, 'getProductDetail']);

