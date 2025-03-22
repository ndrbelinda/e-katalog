<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('list-produk');
// });

// Route::get('/detail', function () {
//     return view('detail-produk');
// });


// <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Route untuk halaman list produk
Route::get('/', [ProductController::class, 'index']);

// Route untuk halaman detail produk
Route::get('/detail/{id}', [ProductController::class, 'showDetail']);