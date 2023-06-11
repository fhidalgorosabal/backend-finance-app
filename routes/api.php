<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ConceptController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BankController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('concept', ConceptController::class);

Route::post('concept/list', [ ConceptController::class, 'list' ]);

Route::apiResource('currency', CurrencyController::class);

Route::apiResource('receipt', ReceiptController::class);

Route::post('receipt/list', [ ReceiptController::class, 'list' ]);

Route::apiResource('account', AccountController::class);

Route::apiResource('bank', BankController::class);
