<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
// use App\Http\Controllers\Api\SegmentationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::middleware(['auth:sanctum', 'role:factory'])->prefix('suppliers')->group(function () {
        // Route::get('{supplier}/products', [SupplierProductController::class, 'getProducts']);
    });

    // Products
    Route::apiResource('products', ApiProductController::class);

    // Orders
    Route::apiResource('orders', ApiOrderController::class);

    // Machine Learning Segmentation & Prediction (example)
    // Route::post('ml/predict', [SegmentationController::class, 'predict']);
    // Route::post('ml/segment', [SegmentationController::class, 'segment']);

    // Logout
    Route::post('logout', [AuthController::class, 'logout']);
});

// Fallback for API 404
Route::fallback(function () {
    return response()->json([
        'message' => 'Resource not found.'
    ], 404);
});
