<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, "login"])->name("auth.login");
Route::post('/register', [AuthController::class, "register"])->name("auth.register");

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);


    Route::get('tasks', [TaskController::class, 'index'])->name('task.index');
    Route::post('tasks', [TaskController::class, 'store'])->name('task.store');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('task.update');
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('task.show');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
    Route::put('tasks/{task}/mark-as-completed', [TaskController::class, 'updateTaskAsCompeleted'])->name('task.destroy');
});
