<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TranslationController;

Route::middleware(['translation.admin'])->group(function () {
    Route::get('/translations/messages', [TranslationController::class, 'show']);
    Route::post('/translations/messages', [TranslationController::class, 'update']);
});
