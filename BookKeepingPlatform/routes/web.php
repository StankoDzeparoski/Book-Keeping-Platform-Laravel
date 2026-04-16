<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentHistoryController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('equipment', EquipmentController::class);
Route::post('equipment/{equipment}/loan', [EquipmentController::class, 'loan'])->name('equipment.loan');
Route::post('equipment/{equipment}/return', [EquipmentController::class, 'return'])->name('equipment.return');

Route::resource('equipmentHistory', EquipmentHistoryController::class);
Route::resource('maintenanceRecord', MaintenanceRecordController::class);
Route::resource('users', UserController::class);


