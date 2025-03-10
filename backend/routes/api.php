<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanesMaestros\Empleados\EmpleadosController;
use App\Http\Controllers\Roles\RolesController;

Route::group([
    'prefix' => 'auth',
], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});



Route::group([
    // 'middleware' => 'api',
    'prefix' => 'auth',
    'middleware' => ['auth:api']
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});



Route::group([
    'middleware' => ['auth:api'],
], function ($router) {
    Route::prefix('accesos')->name('accesos.')->group(function () {
        Route::controller(RolesController::class)->group(function () {
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', "inicio")->name('inicio');
                Route::post('/guardar/{id?}', "guardar")->name('guardar');
                Route::get('/eliminar/{id}', "eliminar")->name('eliminar');
            });
        });
    });

    Route::prefix('planesMaestros')->name('planesMaestros.')->group(function () {
        Route::controller(EmpleadosController::class)->group(function () {
            Route::prefix('empleados')->name('empleados.')->group(function () {
                Route::get('/', "inicio")->name('inicio');
                Route::post('/guardar/{id?}', "guardar")->name('guardar');
            });
        });
    });
});
