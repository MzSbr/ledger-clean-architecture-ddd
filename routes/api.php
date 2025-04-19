<?php

use App\Ledger\Account\Presentation\API\AccountController;
use App\Journal\Entry\Presentation\API\JournalEntryController;
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

// Account Routes
Route::prefix('accounts')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'create']);
    Route::get('/{id}', [AccountController::class, 'show']);
    Route::put('/{id}', [AccountController::class, 'update']);
    Route::post('/{id}/close', [AccountController::class, 'close']);
});

// Journal Entry Routes
Route::prefix('journal-entries')->group(function () {
    Route::get('/', [JournalEntryController::class, 'index']);
    Route::post('/', [JournalEntryController::class, 'create']);
    Route::get('/{id}', [JournalEntryController::class, 'show']);
    Route::post('/{id}/details', [JournalEntryController::class, 'addDetail']);
    Route::post('/{id}/post', [JournalEntryController::class, 'post']);
    Route::post('/{id}/reverse', [JournalEntryController::class, 'reverse']);
});
