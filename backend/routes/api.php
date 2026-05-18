<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TabController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/tabs', [TabController::class, 'index']);
Route::get('/tabs/{tab}', [TabController::class, 'show']);
Route::get('/tabs/{tab}/comments', [CommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/profile', [ProfileController::class, 'show']);

    Route::post('/tabs', [TabController::class, 'store']);
    Route::put('/tabs/{tab}', [TabController::class, 'update']);
    Route::delete('/tabs/{tab}', [TabController::class, 'destroy']);

    Route::post('/tabs/{tab}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/comments/{comment}/vote', [CommentController::class, 'vote']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{tab}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{tab}', [FavoriteController::class, 'destroy']);

    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);
        Route::get('/tabs', [AdminController::class, 'tabs']);
    });
});
