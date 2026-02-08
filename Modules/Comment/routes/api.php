<?php

use Illuminate\Support\Facades\Route;
use Modules\Comment\Http\Controllers\V1\CommentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('/tasks/{taskId}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/tasks/{taskId}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
