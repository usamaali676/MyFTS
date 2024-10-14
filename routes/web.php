<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\PermissionMiddelware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(RoleController::class)
    ->prefix('role')
    ->as('role.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('/detail/{id}', 'show')->name('detail');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('conf-delete/{id}', 'delete')->name('conf-delete');
        Route::get('delete/{id}', 'destroy')->name('delete');
    });
    Route::controller(UserController::class)
    ->prefix('user')
    ->as('user.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('/detail/{id}', 'show')->name('detail');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('conf-delete/{id}', 'delete')->name('conf-delete');
        Route::get('delete/{id}','destroy')->name('delete');
    });
