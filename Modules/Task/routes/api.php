<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\V1\TaskController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('/tasks/assigned-to-me', [TaskController::class, 'assignedToMe'])->name('tasks.assigned-to-me');
    Route::apiResource('tasks', TaskController::class);
});
