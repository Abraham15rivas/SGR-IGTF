<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Rutas de autenticación
Auth::routes();

// Rutas de los usuarios
Route::get('/', function() { 
    return redirect()->to('home'); 
})->name('root');
// rutas
Route::get('/home', [HomeController::class, 'index'])->name('home');
