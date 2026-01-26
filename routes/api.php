<?php

use App\Http\Controllers\ConsolidatedOrderController;
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

// Consolidated Orders API Routes
Route::prefix('consolidated-orders')->middleware(['api'])->group(function () {
    Route::get('/', [ConsolidatedOrderController::class, 'index']);
    Route::post('/populate', [ConsolidatedOrderController::class, 'populate']);
    Route::get('/export', [ConsolidatedOrderController::class, 'export']);
    Route::post('/import', [ConsolidatedOrderController::class, 'import']);
});
