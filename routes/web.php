<?php
// Modelos del framework
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Modelos de los controladores
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Profile\ProfileController;

// Rutas de autenticación
Auth::routes();

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
    // Rutas compartidas de los reportes
    Route::middleware(['admin-analyst'])->group(function () {
        Route::get('/show/excel/core', [HomeController::class, 'showCoreExcel'])->name('show.core.excel');
        Route::get('/show/excel/preliminary', [HomeController::class, 'showPreliminaryExcel'])->name('show.preliminary.excel');
        Route::get('/show/excel/definitive', [HomeController::class, 'showDefinitiveExcel'])->name('show.definitive.excel');
    });
    // Grupo de rutas del analista
    Route::group([
        'middleware' => 'analyst'
    ], function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
    });
    // Grupo de rutas del usuario de seguridad
    Route::group([
        'middleware'    => 'security',
    ], function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
    });
});
