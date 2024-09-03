<?php

use App\Http\Controllers\AuthControlller;
use App\Http\Controllers\CategoryControlller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeControlller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function(){

});


Route::prefix('v1')->group(function () {
    Route::get('/categories', [HomeController::class, 'categories']);

    Route::get('/recipes', [HomeController::class, 'recipes']);
    Route::get('/recipes/{slug}', [HomeController::class, 'show']);

    Route::get('/best-recipes', [HomeController::class, 'top']);

    Route::post('/register', [AuthControlller::class, 'register']);
    Route::post('/login', [AuthControlller::class, 'login']);

    Route::middleware('user')->group(function () {
        // endpoint login ada di sini
        Route::post('/logout', [AuthControlller::class, 'logout']);
        Route::get('/profile', [AuthControlller::class, 'profile']);

        Route::middleware('admin')->group(function () {
            // endpoint login ada di sini
            Route::post('/categories', [CategoryControlller::class, 'store']);
            Route::delete('/categories/{slug}', [CategoryControlller::class, 'destroy']);
        });

        Route::post('/recipes', [RecipeControlller::class, 'store']);
        Route::delete('/recipes/{slug}', [RecipeControlller::class, 'destroy']);

        Route::post('/recipes/{slug}/rating', [RecipeControlller::class, 'rating']);
        Route::post('/recipes/{slug}/comment', [RecipeControlller::class, 'comment']);
    });
});
