<?php

namespace App\Providers;

use App\Models\Equipment;
use App\Models\EquipmentHistory;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Observers\EquipmentHistoryObserver;
use App\Observers\EquipmentObserver;
use App\Observers\MaintenanceRecordObserver;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        // Register observers
        User::observe(UserObserver::class);
        Equipment::observe(EquipmentObserver::class);
        EquipmentHistory::observe(EquipmentHistoryObserver::class);
        MaintenanceRecord::observe(MaintenanceRecordObserver::class);
    }
}
