<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\TransactionsController;
use App\Http\Controllers\Api\IngredientsController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\ProductIngredientsController;  
use App\Http\Controllers\Api\AuditLogController;  
use App\Http\Controllers\Api\NotificationController;  
use App\Http\Controllers\Api\ReportController;  

// =========================
// AUTH
// =========================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('reports')
    ->middleware(['auth:sanctum']) // sesuaikan middleware-mu
    ->group(function () {
        Route::get('sales-summary', [ReportController::class, 'salesSummary']);
        Route::get('today-sales',   [ReportController::class, 'todaySales']);
        Route::get('best-sellers',  [ReportController::class, 'bestSellers']);
        Route::get('low-stock',     [ReportController::class, 'lowStock']);
        Route::get('sales-chart',   [ReportController::class, 'salesChart']);
    });


// =========================
// CATEGORIES
// =========================
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoriesController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CategoriesController::class, 'store']);
        Route::put('/{id}', [CategoriesController::class, 'update']);
        Route::delete('/{id}', [CategoriesController::class, 'destroy']);
    });
});


// =========================
// PRODUCTS
// =========================
Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index']);
    Route::get('/{id}', [ProductsController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ProductsController::class, 'store']);
        Route::put('/{id}', [ProductsController::class, 'update']);
        Route::delete('/{id}', [ProductsController::class, 'destroy']);
    });
});


// =========================
// INGREDIENTS (🔥 BARU)
// =========================
Route::prefix('ingredients')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [IngredientsController::class, 'index']);
        Route::get('/{id}', [IngredientsController::class, 'show']);

        Route::post('/', [IngredientsController::class, 'store']);
        Route::put('/{id}', [IngredientsController::class, 'update']);
        Route::delete('/{id}', [IngredientsController::class, 'destroy']);

        // 🔥 RESTOCK
        Route::post('/{id}/restock', [IngredientsController::class, 'restock']);
    });
});


Route::prefix('product-ingredients')->middleware('auth:sanctum')->group(function () {

    Route::post('/', [ProductIngredientsController::class, 'store']);
    Route::put('/{id}', [ProductIngredientsController::class, 'update']);
    Route::delete('/{id}', [ProductIngredientsController::class, 'destroy']);

});

Route::get('products/{id}/ingredients', [ProductIngredientsController::class, 'index'])
    ->middleware('auth:sanctum');

// 🔥 bulk update resep
Route::post('products/{id}/ingredients/bulk', [ProductIngredientsController::class, 'bulkStore'])
    ->middleware('auth:sanctum');


// =========================
// TRANSACTIONS
// =========================
Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {

    Route::post('/', [TransactionsController::class, 'store']);
    Route::get('/', [TransactionsController::class, 'index']);
    Route::get('/{id}', [TransactionsController::class, 'show']);

});


// =========================
// STOCK MOVEMENTS (🔥 BARU)
// =========================
Route::prefix('stock-movements')->middleware('auth:sanctum')->group(function () {

    Route::get('/', [StockMovementController::class, 'index']);
    Route::get('/{id}', [StockMovementController::class, 'show']);

    // 🔥 manual adjustment (optional)
    Route::post('/adjust', [StockMovementController::class, 'adjust']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});

Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {

    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/save-token', [NotificationController::class, 'saveToken']);
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);

});