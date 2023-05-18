<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware([
//    'auth:api',
])->namespace("\App\Http\Controllers")
    ->group(function () {
        Route::get('/store', [StoreController::class, 'index'])
            ->name('index');

        Route::get('/store/{store}', [StoreController::class, 'show'])
            ->name('show')->whereNumber('store');

        Route::post('/store', [StoreController::class, 'store'])->name('store');

        Route::patch('/store/{store}', [StoreController::class, 'update'])
            ->name('update')->whereNumber('store');

        Route::delete('/store/{store}', [StoreController::class, 'destroy'])->name('destroy');

        Route::post('/store/search', [StoreController::class, 'search'])
            ->name('search');
    });


