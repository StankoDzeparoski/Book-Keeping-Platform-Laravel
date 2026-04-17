<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentHistoryController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource routes for equipment management (accessible to all authenticated users)
    Route::resource('equipment', EquipmentController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/equipment/{equipment}/loan', [EquipmentController::class, 'loan'])->name('equipment.loan');
    Route::post('/equipment/{equipment}/return', [EquipmentController::class, 'return'])->name('equipment.return');

    // Manager-only routes
    Route::middleware(['manager'])->group(function () {
        Route::resource('equipment', EquipmentController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::post('/equipment/{equipment}/repair', [EquipmentController::class, 'repair'])->name('equipment.repair');
        Route::post('/equipment/{equipment}/finish-repair', [EquipmentController::class, 'finishRepair'])->name('equipment.finishRepair');
        Route::resource('equipmentHistory', EquipmentHistoryController::class);
        Route::resource('maintenanceRecord', MaintenanceRecordController::class);
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
