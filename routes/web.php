<?php
// Modelos del framework
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Modelos de los controladores
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Analyst\AnalystController;

// Rutas de autenticación
Auth::routes();

// Token CSRF de la session activa actualmente
Route::get('/csrf-token-active', function(Request $request) {
    return $request->session()->token();
});

// Rutas de los usuarios
Route::get('/', function(Request $request) {
    if($request->user() != null) {
        if($request->user()->role_id == 1) {
            return redirect()->to('admin');
        } else {
            return redirect()->to('home'); 
        }
    } else {
        return redirect()->to('login');
    }
})->name('root');

// grupos de rutas usuarios autenticados
Route::group([
    'middleware' => 'auth'
], function () {
    // Rutas de perfil
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/profile/index', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');
    Route::post('/profile/update/{profile}', [ProfileController::class, 'update'])->name('profile.update');
    
    // Rutas cambio de password del usuario
    Route::get('/profile/change/password', [ProfileController::class, 'changePassword'])->name('profile.pass');
    Route::put('/profile/update/password/{user}', [ProfileController::class, 'setPassword'])->name('profile.update.pass');
    
    // Rutas compartidas para Gerentes y analistas
    Route::middleware(['manager-analyst'])->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/index/excel/transaction', [HomeController::class, 'indexTransaction'])->name('index.transaction.excel');
        Route::get('/show/excel/transaction/{date}', [HomeController::class, 'showTransactionExcel'])->name('show.transaction.excel');
        Route::post('/store/excel/confirmation', [HomeController::class, 'confirmationExcel'])->name('store.confirmation.excel');
        Route::get('/show/xml/transaction', [HomeController::class, 'indexXML'])->name('index.xml');
        Route::get('/show/xml/transaction/{date}', [HomeController::class, 'showXML'])->name('show.xml');
    });
    
    // Rutas compartidas para Gerentes y Administrador
    Route::middleware(['admin-manager'])->group(function () {
        Route::get('/audit/log', [ManagerController::class, 'log'])->name('audit.log');
        Route::get('/audit/statistics', [ManagerController::class, 'statistics'])->name('audit.statistics');
    });

    // Grupo de rutas del analista
    Route::group([
        'middleware' => 'analyst',
        'prefix'     => 'analyst'
    ], function () {
        // Code
    });
    
    // Grupo de rutas del usuario Gerente
    Route::group([
        'middleware' => 'manager',
        'prefix'     => 'manager'
    ], function () {
        Route::get('/calendar', [ManagerController::class, 'index'])->name('calendar');
        Route::post('/calendar/store', [ManagerController::class, 'store']);
        Route::delete('/calendar/delete/{holiday}', [ManagerController::class, 'destroy']);
    });
});
