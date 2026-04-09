<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\UserController;

Route::get('/', [SalesOrderController::class, 'index']);
Route::get('/salesorders', [SalesOrderController::class, 'index'])->name('salesorders.index');
Route::get('/salesorders/{id}', [SalesOrderController::class, 'show'])->name('salesorders.show');

Route::get('/purchaseorders', [PurchaseOrderController::class, 'index'])->name('purchaseorders.index');
Route::get('/purchaseorders/{id}', [PurchaseOrderController::class, 'show'])->name('purchaseorders.show');

Route::get('/customers', [UserController::class, 'index'])->name('customers.index');