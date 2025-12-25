<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\app\Http\Controllers\EmployeeController;
use Modules\Employee\app\Http\Controllers\EmployeeSalaryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {
    Route::resource('employee', EmployeeController::class)->names('employee');
    Route::get('employee/status/{id}', [EmployeeController::class, 'status'])->name('employee.status');
    Route::get('employee/{id}/salary-view/', [EmployeeSalaryController::class, 'index'])->name('employee.salary.view');
    Route::get('employee/{id}/salary-edit/', [EmployeeSalaryController::class, 'edit'])->name('employee.salary.edit');
    Route::delete('employee/{id}/salary-delete/', [EmployeeSalaryController::class, 'destroy'])->name('employee.salary.destroy');
    Route::put('employee/{id}/salary-update/', [EmployeeSalaryController::class, 'update'])->name('employee.salary.update');
    Route::get('employee/{id}/salary-pay/', [EmployeeSalaryController::class, 'create'])->name('employee.salary.create');
    Route::post('employee/{id}/salary-pay/', [EmployeeSalaryController::class, 'store'])->name('employee.salary.store');
    Route::get('employee/{id}/salary-pay/info', [EmployeeSalaryController::class, 'salaryInfo'])->name('employee.salary.info');

    Route::get('salary-list', [EmployeeSalaryController::class, 'salaryList'])->name('salary.list');
});
