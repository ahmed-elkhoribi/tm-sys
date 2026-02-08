<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\V1\TaskController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tasks', TaskController::class)->names('task');
});
