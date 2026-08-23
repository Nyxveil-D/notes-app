<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthTokenController::class, 'store'])->middleware('throttle:api-login');

Route::middleware(['auth:sanctum', 'throttle:api-general'])->group(function () {
    Route::post('/logout', [AuthTokenController::class, 'destroy']);
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::get('/notes/{note}', [NoteController::class, 'show']);
    Route::patch('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
});
