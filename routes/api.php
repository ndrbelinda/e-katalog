<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Route untuk mengambil semua produk
Route::get('/products', [ProductController::class, 'getAllProducts']);

// Route untuk mengambil detail produk berdasarkan ID
Route::get('/products/{id}', [ProductController::class, 'getProductDetail']);