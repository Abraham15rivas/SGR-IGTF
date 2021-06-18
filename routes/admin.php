<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;

// Grupo de rutas del admin o gerente
Route::group([
    'middleware' => ['admin', 'auth']
], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/list/users', [HomeController::class, 'allUsers'])->name('user.all');
    Route::get('/list/users/data', [HomeController::class, 'dataUserList'])->name('user.json');
    Route::put('/change/status/user/{user}', [HomeController::class, 'changeStatus']);
    Route::get('/detail/user/{user}', [HomeController::class, 'detailUser']);
    Route::get('/roles', [HomeController::class, 'allRoles']);
    Route::post('/register', [HomeController::class, 'register']);
    Route::post('/update/{user}', [HomeController::class, 'update']);
});