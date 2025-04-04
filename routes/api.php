<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Definisci la route per la creazione di un prodotto
Route::post('/products', [ProductController::class, 'store']);
