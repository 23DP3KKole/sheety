<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\TabController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/tabs', [TabController::class, 'index']);
Route::get('/tabs/{tab}', [TabController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/tabs', [TabController::class, 'store']);
    Route::put('/tabs/{tab}', [TabController::class, 'update']);
    Route::delete('/tabs/{tab}', [TabController::class, 'destroy']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{tab}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{tab}', [FavoriteController::class, 'destroy']);

    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);
        Route::get('/tabs', [AdminController::class, 'tabs']);
    });
});
