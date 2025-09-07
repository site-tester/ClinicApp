<?php

use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\Admin\InventoryMovementsCrudController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('patient', 'PatientCrudController');
    Route::crud('inventory', 'InventoryCrudController');
    Route::crud('inventory-category', 'InventoryCategoryCrudController');
    Route::get('inventory/{id}/manage-stock', [InventoryMovementsCrudController::class, 'manageStock'])->name('inventory.manage-stock');
    Route::post('inventory/{id}/update-stock', [InventoryMovementsCrudController::class, 'updateStock'])->name('inventory.update-stock');
    Route::crud('inventory-movements', 'InventoryMovementsCrudController');
    Route::crud('appointment', 'AppointmentCrudController');
    Route::get('appointment/book', [AppointmentBookingController::class, 'showForm'])->name('appointment.booking.form');
    Route::post('appointment/book', [AppointmentBookingController::class, 'store'])->name('appointment.booking.store');
    Route::post('appointment/get-available-employees', [AppointmentBookingController::class, 'getAvailableEmployees']);
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
