<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesOrderController;

Route::get('/', [SalesOrderController::class, 'index']);
Route::get('/salesorders', [SalesOrderController::class, 'index'])->name('salesorders.index');
Route::get('/salesorders/{id}', [SalesOrderController::class, 'show'])->name('salesorders.show');
