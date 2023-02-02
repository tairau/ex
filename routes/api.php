<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', 'me');
        });
        Route::middleware('guest:sanctum')->group(function () {
            Route::post('login', 'login');
            Route::post('registration', 'registration');
        });
    });

Route::prefix('currencies')
    ->middleware('auth:sanctum')
    ->controller(CurrencyController::class)
    ->group(function () {
        Route::get('/', 'all');
    });

Route::prefix('wallets')
    ->middleware('auth:sanctum')
    ->controller(WalletController::class)
    ->group(function () {
        Route::get('/', 'all');
        Route::post('/', 'open');
    });

Route::prefix('purchases')
    ->middleware('auth:sanctum')
    ->controller(PurchaseController::class)
    ->group(function () {
        Route::post('/', 'add');
    });

Route::prefix('exchanges')
    ->middleware('auth:sanctum')
    ->controller(ExchangeController::class)
    ->group(function () {
        Route::get('/', 'all');
        Route::post('/', 'bid');
        Route::delete('/{id}', 'cancel');
    });
