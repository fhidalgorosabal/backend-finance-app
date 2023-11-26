<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConceptController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/
Route::post('/register', [ AuthController::class, 'register']);
Route::post('/login', [ AuthController::class, 'login']);

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/profile', [ AuthController::class, 'profile']);
    Route::post('/refresh', [ AuthController::class, 'refresh']);
    Route::post('/logout', [ AuthController::class, 'logout']);
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::apiResource('concept', ConceptController::class);

    Route::post('concept/list', [ ConceptController::class, 'list' ]);

    Route::apiResource('currency', CurrencyController::class);

    Route::apiResource('receipt', ReceiptController::class);

    Route::post('receipt/list', [ ReceiptController::class, 'list' ]);

    Route::apiResource('account', AccountController::class);

    Route::apiResource('bank', BankController::class);

    Route::get('setting', [ SettingController::class, 'getSetting' ]);

    Route::post('setting/change-month', [ SettingController::class, 'changeMonth' ]);
});
