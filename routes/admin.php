<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;

// Grupo de rutas del admin o gerente
Route::group([
    'middleware' => ['admin', 'auth']
], function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/list/users', [HomeController::class, 'allUsers'])->name('user.all');
});