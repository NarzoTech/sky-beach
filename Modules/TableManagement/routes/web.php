<?php

use Illuminate\Support\Facades\Route;
use Modules\TableManagement\app\Http\Controllers\TableController;
use Modules\TableManagement\app\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    // Table Management Routes
    Route::prefix('tables')->group(function () {
        Route::get('/', [TableController::class, 'index'])->name('tables.index');
        Route::get('/create', [TableController::class, 'create'])->name('tables.create');
        Route::post('/', [TableController::class, 'store'])->name('tables.store');
        Route::get('/{id}', [TableController::class, 'show'])->name('tables.show');
        Route::get('/{id}/edit', [TableController::class, 'edit'])->name('tables.edit');
        Route::put('/{id}', [TableController::class, 'update'])->name('tables.update');
        Route::delete('/{id}', [TableController::class, 'destroy'])->name('tables.destroy');

        // Additional Table Routes
        Route::get('/layout/view', [TableController::class, 'layout'])->name('tables.layout');
        Route::post('/status/{id}', [TableController::class, 'updateStatus'])->name('tables.status');
        Route::post('/positions/update', [TableController::class, 'updatePositions'])->name('tables.positions');
        Route::get('/available/list', [TableController::class, 'getAvailable'])->name('tables.available');
        Route::post('/release/{id}', [TableController::class, 'releaseTable'])->name('tables.release');
    });

    // Reservation Routes
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/{id}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('/{id}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/{id}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

        // Reservation Status Routes
        Route::post('/{id}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
        Route::post('/{id}/seat', [ReservationController::class, 'seat'])->name('reservations.seat');
        Route::post('/{id}/complete', [ReservationController::class, 'complete'])->name('reservations.complete');
        Route::post('/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::post('/{id}/no-show', [ReservationController::class, 'markNoShow'])->name('reservations.noshow');

        // Additional Reservation Routes
        Route::get('/view/today', [ReservationController::class, 'today'])->name('reservations.today');
        Route::get('/view/calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::get('/api/timeslots', [ReservationController::class, 'getAvailableTimeslots'])->name('reservations.timeslots');
        Route::get('/api/availability', [ReservationController::class, 'checkAvailability'])->name('reservations.availability');
    });
});
