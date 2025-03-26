<?php

use App\Http\Controllers\ChargeBackController;
use App\Http\Controllers\ClientReportingController;
use App\Http\Controllers\ClientServicesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\InvoiceServiceChargesController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceAreaController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\PermissionMiddelware;
use App\Models\InvoiceServiceCharges;
use App\Models\ServiceArea;
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
   Route::get('/countries', 'getCountries')->name('countries');
    Route::get('/states/{countryId}', 'getStates')->name('states');
    Route::get('/cities/{stateId}/{conrtyId}', 'getCities')->name('cities');
    Route::get('/invoice-price', 'getInvoicePrice')->name('invoicePrice');
    Route::get('/invoice/{invoiceNumber}', 'getInvoice')->name('invoiceView');
    Route::get('/print/{invoiceNumber}', 'invoicePrint')->name('invoicePrint');
    Route::get('/getzelle', 'getzelle')->name('getzelle');
    Route::get('/getcash', 'getcash')->name('getcash');
    Route::get('/getbank', 'getbank')->name('getbank');
    Route::get('/get-refund', 'getRefund')->name('getRefund');
    Route::get('/get-chargeBack', 'getchargeBack')->name('getchargeBack');
    Route::get('/get-ususer', 'getususer')->name('getususer');
    Route::get('otp-verify',  'showVerifyForm')->name('otp.verify');
    Route::post('otp-verify',  'verify')->name('otp.verify.post');

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
    ->prefix('saleInfo')
    ->as('saleInfo.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'sale_info')->name('store');
    });

    Route::controller(ClientServicesController::class)
    ->prefix('clientServices')
    ->as('clientServices.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('search', 'search_services')->name('search_services');
        Route::post('store', 'store')->name('store');
        Route::post('sync-services', 'sync_comp_services')->name('create');
        Route::get('delete', 'destroy')->name('delete');
    });

    Route::controller(ServiceAreaController::class)
    ->prefix('service-area')
    ->as('serviceArea.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
        Route::get('delete', 'destroy')->name('delete');
    });

    Route::controller(KeywordController::class)
    ->prefix('keyword')
    ->as('keyword.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
        Route::get('delete', 'destroy')->name('delete');
    });

    Route::controller(InvoiceServiceChargesController::class)
    ->prefix('invoice_charges')
    ->as('invoiceCharges.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });

    Route::controller(PaymentController::class)
    ->prefix('payment')
    ->as('payment.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });

    Route::controller(CommentsController::class)
    ->prefix('comment')
    ->as('comment.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });

    Route::controller(RefundController::class)
    ->prefix('refund')
    ->as('refund.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });

    Route::controller(ChargeBackController::class)
    ->prefix('chargeback')
    ->as('chargeback.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
    });


    Route::controller(ClientReportingController::class)
    ->prefix('clientReport')
    ->as('clientReport.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::post('store', 'store')->name('store');
        Route::post('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('delete', 'destroy')->name('delete');
    });
