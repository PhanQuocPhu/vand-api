<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware([
//    'auth:api',
])->namespace("\App\Http\Controllers")
    ->group(function () {
        Route::get('/product', [ProductController::class, 'index'])
            ->name('index');

        Route::get('/product/{product}', [ProductController::class, 'show'])
            ->name('show')->whereNumber('product');

        Route::post('/product', [ProductController::class, 'store'])->name('store');

        Route::patch('/product/{product}', [ProductController::class, 'update'])
            ->name('update')->whereNumber('product');

        Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('destroy');

        Route::post('/product/search', [ProductController::class, 'search'])
            ->name('search');

    });
