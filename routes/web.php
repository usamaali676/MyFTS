<?php

use App\Http\Controllers\ClientServicesController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\PermissionMiddelware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(FrontController::class)
->prefix('front')
->as('front.')
// ->middleware(PermissionMiddelware::class)
->group(function () {
   Route::get('get_subcategory', 'get_subcategory')->name('get_subcategory');
   Route::get('/search',  'search')->name('global_search');

});

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

    Route::controller(LeadController::class)
    ->prefix('lead')
    ->as('lead.')
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

    Route::controller(SaleController::class)
    ->prefix('sale')
    ->as('sale.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create/{id}', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('/detail/{id}', 'show')->name('detail');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('conf-delete/{id}', 'delete')->name('conf-delete');
        Route::get('delete/{id}','destroy')->name('delete');
    });

    Route::controller(SaleController::class)
    ->prefix('sale_info')
    ->as('sale_info.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'sale_info')->name('store');
    });

    Route::controller(ClientServicesController::class)
    ->prefix('client_services')
    ->as('client_services.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('search', 'search_services')->name('search_services');
        Route::post('store', 'store')->name('store');
        Route::post('sync-services', 'sync_comp_services')->name('create');
    });
